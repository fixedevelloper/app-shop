<?php


namespace App\Controller;


use App\Entity\Article;
use App\Entity\Caisse;
use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Image;
use App\Entity\SellerShop;
use App\Entity\Shop;
use App\Repository\ArticleRepository;
use App\Repository\CaisseRepository;
use App\Repository\CategoryRepository;
use App\Repository\CustomerRepository;
use App\Repository\ImageRepository;
use App\Repository\SellerShopRepository;
use App\Repository\ShopRepository;
use App\Repository\StockRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StaticController extends  AbstractFOSRestController
{
    private $passwordEncoder;
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $userRepository;
    private $customerRepository;
    private $doctrine;
    private $articleRepository;
    private $categoryRepository;
    private $imageRepository;
    private $shopRepository;
    private $caisseRepository;
    private $stockRepository;
    private $sellerShopRepository;

    /**
     * StaticController constructor.
     * @param SellerShopRepository $sellerShopRepository
     * @param StockRepository $stockRepository
     * @param CaisseRepository $caisseRepository
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param CustomerRepository $customerRepository
     * @param EntityManagerInterface $doctrine
     * @param ArticleRepository $articleRepository
     * @param CategoryRepository $categoryRepository
     * @param ImageRepository $imageRepository
     * @param ShopRepository $shopRepository
     */
    public function __construct(SellerShopRepository $sellerShopRepository,StockRepository $stockRepository,CaisseRepository $caisseRepository,UserPasswordHasherInterface $passwordEncoder, LoggerInterface $logger,UserRepository $userRepository,
                                CustomerRepository $customerRepository, EntityManagerInterface $doctrine,
                                 ArticleRepository $articleRepository, CategoryRepository $categoryRepository,
                                 ImageRepository $imageRepository, ShopRepository $shopRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->doctrine = $doctrine;
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageRepository = $imageRepository;
        $this->shopRepository = $shopRepository;
        $this->caisseRepository=$caisseRepository;
        $this->stockRepository=$stockRepository;
        $this->sellerShopRepository=$sellerShopRepository;
    }

    /**
     * @Rest\Post("/v1/customers", name="api_customers_post")
     * @param Request $request
     * @return Response
     */
    public function customerPost(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];
        if (!is_null($data['id'])) {
            $item = $this->customerRepository->find($data['id']);
        } else {
            $item = new Customer();
            $this->doctrine->persist($item);
        }
        $this->doctrine->flush();
        $view = $this->view([], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post("/v1/sellers", name="api_sellers_post")
     * @param Request $request
     * @return Response
     */
    public function sellerPost(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];

        if (!is_null($data['seller']) && !is_null($data['shop'])) {
            $user=$this->userRepository->find($data['seller']);
            $shop=$this->shopRepository->find($data['shop']);
            $caisse=$this->caisseRepository->find($data['caisse']);
            if (is_null($data['caisse'])){
                $user=$this->userRepository->find($data['seller']);
                $shop=$this->shopRepository->find($data['shop']);
                $item=$this->sellerShopRepository->findOneBy(['shop'=>$shop,'seller'=>$user]);
                $item->setCaisse(null);
                $this->doctrine->flush();
                $view = $this->view([], Response::HTTP_OK, []);
                return $this->handleView($view);
            }
            $caisseshop=$this->sellerShopRepository->findOneBy(['shop'=>$shop,'caisse'=>$caisse]);
            if (!is_null($caisseshop)){
                $view = $this->view([], Response::HTTP_CONFLICT, []);
                return $this->handleView($view);
            }
            $item=$this->sellerShopRepository->findOneBy(['shop'=>$shop,'seller'=>$user]);
            if (is_null($item)){
                $item = new SellerShop();
                $item->setShop($shop);
                $item->setSeller($user);
                $item->setSolde(0.0);
                $item->setTotalsell(0.0);
                $this->doctrine->persist($item);
            }
            $item->setCaisse($caisse);
            $item->setIsActivate($data['isactivate']);
        }
        $this->doctrine->flush();
        $view = $this->view([], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post("/v1/categories", name="api_categorie_post")
     * @param Request $request
     * @return Response
     */
    public function categoryPost(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];
        if (is_null($data['id'])) {
            $category = new Category();
            $this->doctrine->persist($category);
        } else {
            $category = $this->categoryRepository->find($data['id']);
        }
        $category->setName($data['name']);
        $category->setDescription($data['description']);
        if (!empty($data['image'])) {
            $image = $this->imageRepository->find($data['image']);
            $category->setImage($image);
        }
        $this->doctrine->flush();
        $view = $this->view([], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post("/v1/images", name="api_image_post")
     * @param Request $request
     * @return Response
     */
    public function imagePost(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res;
        $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/products/';
        if (!is_null($data['id'])) {
            $image = $this->imageRepository->find($data['id']);
        } else {
            $image = new Image();
            $this->doctrine->persist($image);
        }
        if (!empty($data['filename'])) {
            $image_parts = explode(";base64,", $data['filename']);
            if (!empty($image_parts[1])) {
                $image_base64 = base64_decode($image_parts[1]);

                $file = $destination . $data['name'];
                if (file_put_contents($file, $image_base64)) {
                    $image->setSrc('uploads/products/' . $data['name']);
                }
            }
        }
        $image->setName($data['name']);
        if (!empty($data['alt'])) {
            $image->setAlt($data['alt']);
        }
        $this->doctrine->flush();
        $view = $this->view([
            'id' => $image->getId(),
            'name' => $image->getName(),
        ], Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/v1/articles", name="api_article_add")
     * @param Request $request
     * @return Response
     */
    public function articleAction(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];

        if (is_null($data['id'])) {
            $category = $this->categoryRepository->find($data['category']);
            $article = new Article();
            $article->setCategory($category);
            $article->setStatus(Article::VISIBLE);
            $article->setCodebarre($data['codebarre']);
            $this->doctrine->persist($article);
        } else {
            $article = $this->articleRepository->find($data['id']);
        }
        $article->setName($data['name']);
        $article->setDescription($data['description']);
        $article->setPrice($data['price']);

        if (!empty($data['image'])) {
            $image = $this->imageRepository->find($data['image']);
            $article->setImage($image);
        }
        $this->doctrine->flush();
        $view = $this->view([], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post("/v1/shops", name="api_shop_add")
     * @param Request $request
     * @return Response
     */
    public function shopAction(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];

        if (is_null($data['id'])) {
            $shop = new Shop();
            $this->doctrine->persist($shop);
        } else {
            $shop = $this->shopRepository->find($data['id']);
        }
        $shop->setLibelle($data['libelle']);
        $shop->setAddress($data['address']);
        $shop->setPhone($data['phone']);
        $this->doctrine->flush();
        $view = $this->view([], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Post("/v1/caisses", name="api_caisse_add")
     * @param Request $request
     * @return Response
     */
    public function caisseAction(Request $request)
    {
        $res = json_decode($request->getContent(), true);
        $data = $res['data'];

        if (is_null($data['id'])) {
            $caisse = new Caisse();
            $shop=$this->shopRepository->find($data['shop']);
            $caisse->setShop($shop);
            $this->doctrine->persist($caisse);
        } else {
            $caisse = $this->caisseRepository->find($data['id']);
        }
        $caisse->setLibelle($data['libelle']);
        $caisse->setCode($data['code']);
        $caisse->setHasretraitespece($data['hasretraitespece']);
        $caisse->setMaxretraitoperation($data['maxretraitoperation']);
        $caisse->setHastransfertretrait($data['hastransfertretrait']);
        $caisse->setMaxretraitperiode($data['maxretraitperiode']);
        $this->doctrine->flush();
        $view = $this->view([], Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/shops", name="api_shop_list")
     * @param Request $request
     * @return Response
     */
    public function shopList(Request $request)
    {
        $items = $this->shopRepository->findAll();
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'libelle' => $item->getLibelle(),
                'address' => $item->getAddress(),
                'phone' => $item->getPhone(),
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/caisses/{shop}", name="api_caisse_list")
     * @param Request $request
     * @param Shop $shop
     * @return Response
     */
    public function caisseList(Request $request,Shop $shop)
    {
        $items = $this->caisseRepository->findBy(['shop'=>$shop]);
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'libelle' => $item->getLibelle(),
                'code' => $item->getCode(),
                'hasretraitespece' => $item->getHasretraitespece(),
                'maxretraitoperation' => $item->getMaxretraitoperation(),
                'maxretraitperiode' => $item->getMaxretraitperiode(),
                'hastransfertretrait' => $item->getHastransfertretrait(),
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/articles/{shop}", name="api_article_list")
     * @param Request $request
     * @return Response
     */
    public function articleList(Request $request,Shop $shop)
    {
        $items = $this->articleRepository->findAll();

        $data = [];
        foreach ($items as $item) {
            $image = $item->getImage();
            $stock=$this->stockRepository->findOneBy(['shop'=>$shop,'article'=>$item]);
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'description' => $item->getDescription(),
                'category' => $item->getCategory()->getId(),
                'category_name' => $item->getCategory()->getName(),
                'quantity'=>is_null($stock)?0:$stock->getQuantity(),
                'status' => $item->getStatus(),
                'image_id'=>$image->getId(),
                'image' => is_null($image) ? "" : $this->getParameter('domaininit') . $image->getSrc(),
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/categories", name="api_category_list")
     * @param Request $request
     * @return Response
     */
    public function categoryList(Request $request)
    {
        $items = $this->categoryRepository->findAll();
        $data = [];
        foreach ($items as $item) {
            $image = $item->getImage();
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'image_id' => is_null($image) ? "" :$image->getId(),
                'image' => is_null($image) ? "" : $this->getParameter('domaininit') . $image->getSrc(),
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/v1/articles/codebarre/{codebarre}/{shop}", name="api_article_codebarre")
     * @param Request $request
     * @param string $codebarre
     * @param Shop $shop
     * @return Response
     */
    public function articleCodebarre(Request $request, string $codebarre,Shop $shop)
    {
        $item = $this->articleRepository->findOneBy(['codebarre'=>$codebarre]);
        $stock=$this->stockRepository->findOneBy(['shop'=>$shop,'article'=>$item]);
        $image = $item->getImage();
        $data = [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'description' => $item->getDescription(),
            'price' => $item->getPrice(),
            'category' => $item->getCategory()->getId(),
            'category_name' => $item->getCategory()->getName(),
            'status' => $item->getStatus(),
            'imageid'=>$image->getId(),
            'quantity'=>is_null($stock)?0:$stock->getQuantity(),
            'image' => is_null($image) ? "" : $this->getParameter('domaininit') . $image->getSrc(),
        ];

        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/articles/article/{id}/{shop}", name="api_article_one")
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function articleOne(Request $request, Article $article,Shop $shop)
    {
        $item = $article;
        $stock=$this->stockRepository->findOneBy(['shop'=>$shop,'article'=>$article]);
        $image = $item->getImage();
        $data = [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'description' => $item->getDescription(),
            'price' => $item->getPrice(),
            'category' => $item->getCategory()->getId(),
            'category_name' => $item->getCategory()->getName(),
            'status' => $item->getStatus(),
            'imageid'=>$image->getId(),
            'quantity'=>is_null($stock)?0:$stock->getQuantity(),
            'image' => is_null($image) ? "" : $this->getParameter('domaininit') . $image->getSrc(),
        ];

        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/users", name="api_user_list")
     * @param Request $request
     * @return Response
     */
    public function UsersList(Request $request)
    {
        $items = $this->userRepository->findAll();
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'phone' => $item->getPhone(),
                'email' => $item->getEmail(),
                'roles'=>$item->getRoles()[0]
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
    /**
     * @Rest\Get("/v1/sellers/{shop}", name="api_seller_list")
     * @param Request $request
     * @return Response
     */
    public function sellerList(Request $request,Shop $shop)
    {
        $items = $this->sellerShopRepository->findBy(['shop'=>$shop]);
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getSeller()->getName(),
                'seller_id' => $item->getSeller()->getId(),
                'phone' => $item->getSeller()->getPhone(),
                'email' => $item->getSeller()->getEmail(),
                'solde' => $item->getSolde(),
                'totalsell' => $item->getTotalsell(),
                'isactivate' => $item->isIsActivate(),
            ];
        }
        $view = $this->view($data, Response::HTTP_OK, []);
        return $this->handleView($view);
    }
}
