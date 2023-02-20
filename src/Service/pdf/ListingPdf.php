<?php


namespace App\Service\pdf;


use App\Repository\ArticleRepository;
use App\Repository\SaleArticleRepository;

class ListingPdf
{
    private $pdf;
    private $saleRepository;
    private $articleRepository;
    private $params;

    /**
     * ListingPdf constructor.
     * @param $pdf
     * @param $saleRepository
     * @param $articleRepository
     * @param $params
     */
    public function __construct(SaleArticleRepository $saleRepository, ArticleRepository $articleRepository, $params)
    {
        $this->pdf = new MFpdf();
        $this->saleRepository = $saleRepository;
        $this->articleRepository = $articleRepository;
        $this->params = $params;
    }

}
