<?php
/**
 * Created by PhpStorm.
 * User: diman
 * Date: 31.03.19
 * Time: 10:21
 */

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use Doctrine\ORM\EntityManager;



class ProductImportService
{
    public function importCsv()
    {
        $originalFileName = $this->file->getClientOriginalName();
        $file = $this->file->getRealPath();
        //import products from csv and xml

        $arr_file = explode('.', $originalFileName);
        $extension = end($arr_file);
        if('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } elseif ('xml' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
        }
        $spreadsheet = $reader->load($file);
        $importProducts = $spreadsheet->getActiveSheet()->toArray();
        $this->pushProductToBase($importProducts);
        return true;
    }
    private function pushProductToBase($importProducts){
        foreach ($importProducts as $importProduct)
        {
            $importProductId=$importProduct[0];
            $product = $this->repository->findOneBy(['external_id'=>$importProductId]);
            if ($product) {
                $product->setName($importProduct[3]);
                $product->setDescription($importProduct[4]);
                $product->setCount($importProduct[5]);
                $product->setPrice($importProduct[6]);
                // $product->setCategory($importProduct[1]);
                $product->setCategory(0);
            }
            else {
                $product = new Product();
                $product->setName($importProduct[3]);
                $product->setDescription($importProduct[4]);
                $product->setCount($importProduct[5]);
                $product->setPrice($importProduct[6]);
                // $product->setCategory($importProduct[1]);
                $product->setCategory(0);
                $product->setExternalId($importProduct[0]);
            }
            $this->manager->persist($product);
            $this->manager->flush();
        }
        return true;
    }

    public function __construct(EntityManager $manager,$file,$repository)
    {
        $this->manager = $manager;
        $this->file = $file;
        $this->repository = $repository;
    }

}