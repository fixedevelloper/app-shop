<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Caisse;
use App\Entity\JourneeComptable;
use App\Entity\LineSale;
use App\Entity\MouvementCaisse;
use App\Entity\SaleArticle;
use App\Entity\Shop;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\CaisseRepository;
use App\Repository\JourneeComptableRepository;
use App\Repository\LineSaleRepository;
use App\Repository\MouvementCaisseRepository;
use App\Repository\SaleArticleRepository;
use App\Repository\SellerShopRepository;
use App\Repository\ShopRepository;
use App\Repository\StockRepository;
use App\Repository\UserRepository;
use App\Service\pdf\FactureService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ManagerController extends AbstractFOSRestController
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $userRepository;
    private $doctrine;
    private $articleRepository;
    private $shopRepository;
    private $caisseRepository;
    private $stockRepository;
    private $saleRepository;
    private $lineSaleRepository;
    private $sellerShopRepository;
    private $factureService;
    private $journeeComptableRepository;
    private $mouvementRepository;

    /**
     * ManagerController constructor.
     * @param FactureService $factureService
     * @param SellerShopRepository $sellerShopRepository
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $doctrine
     * @param ArticleRepository $articleRepository
     * @param ShopRepository $shopRepository
     * @param CaisseRepository $caisseRepository
     * @param StockRepository $stockRepository
     * @param SaleArticleRepository $saleRepository
     * @param LineSaleRepository $lineSaleRepository
     */
    public function __construct(JourneeComptableRepository $journeeComptableRepository,FactureService $factureService,SellerShopRepository $sellerShopRepository,LoggerInterface $logger, UserRepository $userRepository,
                                EntityManagerInterface $doctrine,ArticleRepository $articleRepository,
                                ShopRepository $shopRepository,CaisseRepository $caisseRepository,
                                StockRepository $stockRepository, SaleArticleRepository $saleRepository,
                                LineSaleRepository $lineSaleRepository,MouvementCaisseRepository $mouvementRepository)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->doctrine = $doctrine;
        $this->articleRepository = $articleRepository;
        $this->shopRepository = $shopRepository;
        $this->caisseRepository = $caisseRepository;
        $this->stockRepository = $stockRepository;
        $this->saleRepository = $saleRepository;
        $this->lineSaleRepository = $lineSaleRepository;
        $this->sellerShopRepository=$sellerShopRepository;
        $this->factureService=$factureService;
        $this->journeeComptableRepository=$journeeComptableRepository;
        $this->mouvementRepository=$mouvementRepository;
    }
    /**
     * @Rest\Post("/v1/sales", name="api_sales_post")
     * @param Request $request
     * @return Response
     */
    public function salePost(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];
        $sale=new SaleArticle();
        $user=$this->sellerShopRepository->find($data['seller']);
        $sale->setSellerShop($user);
        $sale->setCustomerName($data['customer']);
        $sale->setAmount($data['amount']);
        $sale->setAmountTotal($data['amounttc']);
        $sale->setStatus(SaleArticle::DELIVRED);
        $this->doctrine->persist($sale);
        $lines=$data['lines'];
        for ($i=0;$i<sizeof($lines);$i++){
            $product=$this->articleRepository->find($lines[$i]['id']);
            $stock=$this->stockRepository->findOneBy(['article'=>$product,'shop'=>$user->getShop()]);
          if (is_null($stock)|| $stock->getQuantity()<$lines[$i]['quantity']){
              throw new ConflictHttpException("Product is not in stock");
          }
            $stock->setQuantity($stock->getQuantity()-$lines[$i]['quantity']);
            $lineArticle=new LineSale();
            $lineArticle->setArticle($product);
            $lineArticle->setQuantity($lines[$i]['quantity']);
            $lineArticle->setPrice($product->getPrice());
            $lineArticle->setSaleArticle($sale);
            $this->doctrine->persist($lineArticle);
        }
        $this->makeJourneComptable($user->getCaisse(),$data['amounttc']);
        $this->doctrine->flush();
        $view = $this->view([
            'id'=>$sale->getId()
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/sales/{shop}", name="api_sale_search_sshop")
     * @param Shop $shop
     * @param Request $request
     * @return Response
     */
    public function searchSaleBySshop(Shop $shop,Request $request)
    {
        if ($request->get("datebegin")==null){
            $datebegin=date("Y-m-d");
            $dateend=date("Y-m-d");
        }else{
            $datebegin=$request->get("datebegin");
            $dateend=$request->get("dateend");
        }
        $items = $this->saleRepository->findByPeriode($datebegin,$dateend,$shop);
        $data = [];
        foreach ($items as $item) {
            $lines=[];
            foreach ($item->getLineArticles() as $lineArticle){
                $lines[]=[
                    "article_id"=>$lineArticle->getArticle()->getId(),
                    "article"=>$lineArticle->getArticle()->getName(),
                    "price"=>$lineArticle->getArticle()->getPricesell(),
                    "quantity"=>$lineArticle->getQuantity()
                ];
            }
            $data[] = [
                'id' => $item->getId(),
                'customer' => $item->getCustomerName(),
                'seller' => $item->getSellerShop()->getSeller()->getName(),
                'amount' => $item->getAmount(),
                'amounttc' => $item->getAmountTotal(),
                'created_at' => $item->getDateCreated(),
                'shop' => $item->getSellerShop()->getShop()->getLibelle(),
                'shop_id' => $item->getSellerShop()->getShop()->getId(),
                'status' => $item->getStatus(),
                'lines' => $lines,
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/sales", name="api_sale_search_seller")
     * @param Request $request
     * @return Response
     */
    public function searchSaleBySeller(Request $request)
    {
        if ($request->get("datebegin")=='null'){
            $datebegin=date("Y-m-d");
            $dateend=date("Y-m-d");
        }else{
            $datebegin=$request->get("datebegin");
            $dateend=$request->get("dateend");
        }
        $seller=$this->sellerShopRepository->find($request->get("seller"));
        $items = $this->saleRepository->findByPeriodeAndSeller($datebegin,$dateend,$seller);
        $data = [];
        foreach ($items as $item) {
            $lines=[];
            foreach ($item->getLineArticles() as $lineArticle){
                $lines[]=[
                    "article_id"=>$lineArticle->getArticle()->getId(),
                    "article"=>$lineArticle->getArticle()->getName(),
                    "price"=>$lineArticle->getArticle()->getPricesell(),
                    "quantity"=>$lineArticle->getQuantity()
                ];
            }
            $data[] = [
                'id' => $item->getId(),
                'customer' => $item->getCustomerName(),
                'seller' => $item->getSellerShop()->getSeller()->getName(),
                'amount' => $item->getAmount(),
                'amounttc' => $item->getAmountTotal(),
                'created_at' => $item->getDateCreated(),
                'shop' => $item->getSellerShop()->getShop()->getLibelle(),
                'shop_id' => $item->getSellerShop()->getShop()->getId(),
                'caisse' => $item->getSellerShop()->getCaisse()->getId(),
                'status' => $item->getStatus(),
                'lines' => $lines,
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @param SaleArticle $saleArticle
     * @return Response
     * @Rest\Get ("/v1/sales/pdf/{id}",name="getpdfsale")
     */
    public function getFacturePdf(SaleArticle $saleArticle)
    {

        $this->factureService->init($saleArticle);
        $view = $this->view([
            'link' => $this->getParameter('domaininit') . 'facture/'.'facture.pdf'
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @param Article $article
     * @return Response
     * @Rest\Get ("/v1/etiquetes/pdf/{id}/{quantity}",name="getqrcode")
     */
    public function getQrcodePdf(Article $article)
    {
        $this->factureService->initEtiquete($article);
        $view = $this->view([
            'link' => $this->getParameter('domaininit') . 'etiquette/'.$article->getId().'.pdf'
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/v1/retraits", name="api_retrait_post")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function retraitPost(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];
        $caisse=$this->caisseRepository->find($data['caisse']);
        $beneficiary=$this->userRepository->find($data['beneficiary']);
        $retrait=new MouvementCaisse();
        $retrait->setCaisse($caisse);
        $retrait->setDescription($data['description']);
        $retrait->setBenficiary($beneficiary);
        $retrait->setDebit($data['amount']);
        $retrait->setDateoperation(new \DateTimeImmutable("now",new \DateTimeZone("Africa/Brazzaville")));
        $retrait->setDatestring(date("Y-m-d"));
        $retrait->setLibelle("MVR-".$caisse->getId()."-".$beneficiary->getId().$retrait->getDateoperation()->getTimestamp());
        $retrait->setStatus(MouvementCaisse::PENDING);
        $this->doctrine->persist($retrait);
        $this->doctrine->flush();
        $view = $this->view([
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post("/v1/retraits/validation", name="api_retrait_valide_post")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function retraitValidePost(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];
        $mouvement=$this->mouvementRepository->find($data['id']);
        if ($data['status']=="ACCEPTED"){
            $mouvement->setStatus(MouvementCaisse::ACCEPTED);
            $caisse=$mouvement->getCaisse();
            $caisse->setSolde($caisse->getSolde()-$mouvement->getDebit());
        }else{
            $mouvement->setStatus(MouvementCaisse::REFUSED);
        }
        $this->doctrine->flush();
        $view = $this->view([
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/mouvements", name="api_mouvements_search")
     * @param Request $request
     * @return Response
     */
    public function searchMouvement(Request $request)
    {
        if ($request->get("datebegin")=='null'){
            $datebegin=date("Y-m-d");
            $dateend=date("Y-m-d");
        }else{
            $datebegin=$request->get("datebegin");
            $dateend=$request->get("dateend");
        }
        if (!is_null($request->get("caisse"))){
            $caisse=$this->caisseRepository->find($request->get("caisse"));
            $items = $this->mouvementRepository->findByPeriodeAndCaisse($datebegin,$dateend,$caisse);
        }else{
            $shop=$this->shopRepository->find($request->get("shop"));
            $items = $this->mouvementRepository->findByPeriodeshop($datebegin,$dateend,$shop);
        }

        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'beneficiary' => $item->getBenficiary()->getName(),
                'credit' => $item->getCredit(),
                'debit' => $item->getDebit(),
                'created_at' => $item->getDateoperation(),
                'shop' => $item->getCaisse()->getShop()->getLibelle(),
                'shop_id' => $item->getCaisse()->getShop()->getId(),
                'caisse' => $item->getCaisse()->getLibelle(),
                'status' => $item->getStatus(),
                'libelle' => $item->getLibelle(),
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    function makeJourneComptable(Caisse $caisse,$amount){
        $jourcomptable=$this->journeeComptableRepository->findOneBy(['datecomptable'=>new \DateTimeImmutable('now'),'caisse'=>$caisse->getId()]);
        if ($jourcomptable==null){
            $jourcomptable=new JourneeComptable();
            $jourcomptable->setCaisse($caisse);
            $jourcomptable->setStatus(true);
            $jourcomptable->setDatecomptable(new \DateTimeImmutable('now'));
            $jourcomptable->setSoldeouverture($caisse->getSolde());
            $jourcomptable->setVersement($amount);
            $jourcomptable->setRetrait(0.0);
            $jourcomptable->setSoldetheorique($caisse->getSolde()+$amount);

        }else{
            $jourcomptable->setVersement($jourcomptable->getRetrait()+$amount);
            $jourcomptable->setSoldetheorique($jourcomptable->getSoldetheorique()+$amount);
        }
        $this->doctrine->persist($jourcomptable);
    }
    /**
     * @param $data
     * @return Response
     * @Rest\Get  ("/v1/caisses/{caisse}/consulter",name="caisseconsulter")
     */
    public function consultercaisse(Caisse $caisse): Response
    {
        $mouvements=$this->saleRepository->findBy(['caisse'=>$caisse]);
        $data = [];
        foreach ($mouvements as $item) {
            $data[] = [
                'id' => $item->getId(),
                'customer' => $item->getCustomerName(),
                'seller' => $item->getSellerShop()->getSeller()->getName(),
                'amount' => $item->getAmount(),
                'amounttc' => $item->getAmountTotal(),
                'created_at' => $item->getDateCreated(),
                'shop' => $item->getSellerShop()->getShop()->getLibelle(),
                'shop_id' => $item->getSellerShop()->getShop()->getId(),
                'caisse' => $item->getSellerShop()->getCaisse()->getId(),
                'status' => $item->getStatus(),
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @param string $date
     * @param Caisse $caisse
     * @return Response
     * @throws \Exception
     * @Rest\Get  ("/v1/journeecomptable/{date}/{caisse}",name="journeecomptablebydateandcaisse",)
     */
    public function getJouneeComptableByCaisse(string $date,Caisse $caisse): Response
    {
        $datev=new \DateTimeImmutable($date);
        $jour=$this->journeeComptableRepository->findOneBy(['datecomptable'=>$datev,'caisse'=>$caisse]);
        if ($jour==null){
            $values=[
                'datecomptable'=>$datev->format('Y-m-d'),
                'caisse'=>$caisse,
                'versement'=>0.0,
                'retrait'=>0.0,
                'soldeouverture'=>$caisse->getSolde(),
                'soldetheorique'=>0.0,
                'status'=>false
            ];
        }else{
            $values=[
                'datecomptable'=>$datev->format('Y-m-d'),
                'caisse'=>$caisse,
                'versement'=>$jour->getVersement(),
                'retrait'=>$jour->getRetrait(),
                'soldeouverture'=>$jour->getSoldeouverture(),
                'soldetheorique'=>$jour->getSoldetheorique(),
                'status'=>$jour->getStatus()
            ];
        }
        $view = $this->view($values, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @param string $date
     * @param Caisse $caisse
     * @return Response
     * @throws \Exception
     * @Rest\Get  ("/v1/journeecomptable/{date}/{caisse}/cloture",name="cloturejourneecomptablebydateandcaisse",)
     */
    public function clotureJouneeComptableByCaisse(string $date,Caisse $caisse): Response
    {
        $datev=new \DateTimeImmutable($date);
        $jour=$this->journeeComptableRepository->findOneBy(['datecomptable'=>$datev,'caisse'=>$caisse]);
        if ($jour==null){
            $jour=new JourneeComptable();
            $jour->setStatus(false);
            $jour->setSoldetheorique(0.0);
            $jour->setRetrait(0.0);
            $jour->setVersement(0.0);
            $jour->setDatecomptable($datev);
            $jour->setCaisse($caisse);
            $this->doctrine->persist($jour);
        }else{
            $jour->setStatus(false);
        }
        $this->doctrine->flush();
        $view = $this->view($jour, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @param string $date
     * @param Caisse $caisse
     * @return Response
     * @throws \Exception
     * @Rest\Get  ("/v1/journeecomptable/{date}/{caisse}/decloture",name="decloturejourneecomptablebydateandcaisse",)
     */
    public function declotureJouneeComptableByCaisse(string $date,Caisse $caisse): Response
    {
        $datev=new \DateTimeImmutable($date);
        $jour=$this->journeeComptableRepository->findOneBy(['datecomptable'=>$datev,'caisse'=>$caisse]);
        $jour->setStatus(true);
        $this->doctrine->flush();
        $view = $this->view($jour, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @param string $date
     * @param Caisse $caisse
     * @return Response
     * @throws \Exception
     * @Rest\Get  ("/v1/journeecomptable/{date}/{caisse}/open",name="openjourneecomptablebydateandcaisse",)
     */
    public function openJouneeComptableByCaisse(string $date,Caisse $caisse): Response
    {
        $datev=new \DateTimeImmutable($date);
        $jour=$this->journeeComptableRepository->findOneBy(['datecomptable'=>$datev,'caisse'=>$caisse]);
        if ($jour==null){
            $jour=new JourneeComptable();
            $jour->setStatus(true);
            $jour->setSoldetheorique(0.0);
            $jour->setRetrait(0.0);
            $jour->setVersement(0.0);
            $jour->setDatecomptable($datev);
            $jour->setSoldeouverture($caisse->getSolde());
            $jour->setCaisse($caisse);
            $this->doctrine->persist($jour);
        }else{
            $jour->setStatus(true);
        }
        $this->doctrine->flush();
        $view = $this->view($jour, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @param Caisse $caisse
     * @return Response
     * @Rest\Get  ("/v1/journeecomptable/{caisse}",name="noactivejourneecomptablebycaisse",)
     */
    public function getJouneeComptableByCaisseNoCloture(Caisse $caisse): Response
    {
        $jour=$this->journeeComptableRepository->findOneBycaisseactuve($caisse);
        $view = $this->view($jour, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
}
