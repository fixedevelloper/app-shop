<?php


namespace App\Service\pdf;


use App\Entity\Article;
use App\Entity\SaleArticle;
use App\Repository\SaleArticleRepository;
use App\Utils\QRCode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FactureService
{
    private $pdf;
    private $saleRepository;
    private $params;

    /**
     * Facture constructor.
     * @param $configurationRepository
     * @param $saleRepository
     * @param $params
     */
    public function __construct(SaleArticleRepository $saleRepository,ParameterBagInterface $params)
    {
        $this->saleRepository = $saleRepository;
        $this->params = $params;
        $this->pdf=new MFpdf();
    }
    function header()
    {
        $this->pdf->SetFont('Times', '', 10);
        $logo= $this->params->get('domaininit'). 'assets/logo.png';
        if(is_file($logo) && getimagesize($logo)) {
            $this->pdf->Image($logo, 5, 10,40,25,"PNG");
        }

        $this->pdf->SetFont('Times', 'B', 14);
        $this->pdf->SetXY(160, 15);
        $this->pdf->Cell(10, 6, "Ets Agensic", 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetXY(160, 22);
        $this->pdf->Cell(10, 6,"Tel:678968541", 0, 0, 'L');
        $this->pdf->Ln();
    }

    function headerName(SaleArticle $saleArticle)
    {
        $this->pdf->SetXY(10,40);
        $this->pdf->SetFont('Times', 'B', 12);
        $this->pdf->Cell(10, 6, "Client: ".utf8_decode($saleArticle->getCustomerName()), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->Cell(10, 6, "Tel: ", 0, 0, 'L');
        $this->pdf->Ln();
        if (is_null($saleArticle->getSellerShop()->getCaisse())){
            $this->pdf->Cell(10, 6, "Caisse: caisse null", 0, 0, 'L');
        }else{
            $this->pdf->Cell(10, 6, "Caisse: ".utf8_decode($saleArticle->getSellerShop()->getCaisse()->getLibelle()), 0, 0, 'L');
        }
        $this->pdf->Ln();
        $this->pdf->SetXY(130,40);
        $this->pdf->Cell(10, 6, utf8_decode("Facture N° #000".$saleArticle->getId()), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetXY(130,46);
        $this->pdf->Cell(10, 6, utf8_decode("Vendeur:".$saleArticle->getSellerShop()->getSeller()->getName()), 0, 0, 'L');
        $this->pdf->Ln();
        $this->pdf->SetXY(130,52);
        $this->pdf->Cell(10, 6, utf8_decode("Date :".$saleArticle->getDateCreated()->format('Y-m-d')), 0, 0, 'L');

    }

    function headerTable()
    {
        $this->pdf->SetFont('Times', 'B', 12);
        $this->pdf->SetY(60);
        $this->pdf->SetX(5);
        $this->pdf->SetFillColor(113, 113, 113);
        $this->pdf->Cell(20, 8, '#', 1, 0, 'C', true);
        $this->pdf->Cell(100, 8, 'Libelle', 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, 'Prix U', 1, 0, 'C', true);
        $this->pdf->Cell(20, 8, utf8_decode('Quantité'), 1, 0, 'C', true);
        $this->pdf->Cell(30, 8, 'Montant', 1, 0, 'C', true);
        $this->pdf->Ln();
    }

    function bodyTable(SaleArticle $saleArticle)
    {
        $this->headerName($saleArticle);
        $this->headerTable();
        $this->pdf->SetY(68);
        $this->pdf->SetX(5);
        $items=$saleArticle->getLineArticles();
        $h = 68;
        for ($i = 0; $i < sizeof($items); $i++) {
            $this->pdf->SetFont('Times', 'B', 8);
            $this->pdf->SetX(5);
            $this->pdf->SetFillColor(211, 211, 211);
            $this->pdf->Cell(20, 6,utf8_decode($i) , 1, 0, 'L', true);
            $this->pdf->Cell(100, 6,utf8_decode($items[$i]->getArticle()->getName()) , 1, 0, 'L', true);
            $this->pdf->SetFont('Times', '', 8);
            $this->pdf->Cell(30, 6, $items[$i]->getArticle()->getPricesell(), 1, 0, 'C', true);
            $this->pdf->Cell(20, 6, $items[$i]->getQuantity(), 1, 0, 'C', true);
            $this->pdf->Cell(30, 6, $items[$i]->getQuantity()*$items[$i]->getArticle()->getPricesell(), 1, 0, 'C', true);
            $this->pdf->Ln();
            $h=$this->pdf->GetY();
        }
        $this->pdf->SetY($h+1);
        $this->pdf->SetX(125);
        $this->pdf->Cell(50, 8, 'Total', 1, 0, 'R');
        $this->pdf->Cell(30, 8, $saleArticle->getAmount(), 1, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->SetX(125);
        $this->pdf->Cell(50, 8, 'Taxes', 1, 0, 'R');
        $this->pdf->Cell(30, 8, 0.0, 1, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->SetX(125);
        $this->pdf->Cell(50, 8, 'Total Ttc', 1, 0, 'R');
        $this->pdf->Cell(30, 8, $saleArticle->getAmountTotal(), 1, 0, 'C');
        $this->pdf->Ln();
    }

    public function init(SaleArticle $saleArticle)
    {
        $this->pdf->AddPage('P', 'A4', 0);

        $this->pdf->AliasNbPages();
        $this->header();
        $this->bodyTable($saleArticle);
        $dir = "facture";
        if (!is_dir($dir)) {
            mkdir($dir, 0700);
        }

        $this->pdf->Output('F', $dir . '/facture.pdf');
    }
    public function initEtiquete(Article $article)
    {
        $this->pdf->AddPage('P', 'A4', 0);

        $this->pdf->AliasNbPages();
        $this->header();
        $this->bodyQrcode($article);
        $dir = "etiquette/";
        if (!is_dir($dir)) {
            mkdir($dir, 0700);
        }
        $this->pdf->Output('F', $dir  . $article->getId() . '.pdf');
    }

    private function bodyQrcode(Article $article)
    {    $this->pdf->SetFont('Times', '', 18);
        $this->pdf->SetXY(10, $this->pdf->GetY()+5);
        $this->pdf->Cell(200, 10, "Etiquetes Qrcode | Article:".$article->getName(), 0, 0, 'C');
        $this->pdf->Ln(5);
        $this->pdf->SetY($this->pdf->GetY()+20);
        $textqr ="Product: ".$article->getName()."\n Price: ".$article->getPricesell()."\n";
        $qr = QRCode::getMinimumQRCode($textqr, QR_ERROR_CORRECT_LEVEL_L);
        $im = $qr->createImage(5, 2);
        $valimage=imagepng($im, "qr-".$article->getName().'.png');
        if ($valimage){
            $x=5;
            $xx=5;$xxx=5;$xxxx=5;
            for ($i=0;$i<16;$i++){

                switch ($x){
                    case $x<=160:
                    $this->pdf->Image("qr-".$article->getName().'.png', $x, 50);
                    break;
                    case $x>160 and $x<360:
                        $this->pdf->Image("qr-".$article->getName().'.png', $xx, 100);
                        $xx+=50;
                        break;
                    case $x>360 and $x<560:
                        $this->pdf->Image("qr-".$article->getName().'.png', $xxx, 150);
                        $xxx+=50;
                        break;
                    case $x>560:
                        $this->pdf->Image("qr-".$article->getName().'.png', $xxxx, 200);
                        $xxxx+=50;
                        break;
                }
                $x+=50;
            }

        }
    }
}
