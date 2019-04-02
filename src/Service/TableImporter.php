<?php
/**
 * Created by PhpStorm.
 * User: diman
 * Date: 31.03.19
 * Time: 10:21
 */

namespace App\Service;

use App\Entity\Product;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Doctrine\ORM\EntityManager;;



class TableImporter
{
    public function importCsv($file, $repository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $products = $repository->findAll();
        $originalFileName = $file->getClientOriginalName();
        $file = $file->getRealPath();
        //import products from csv and xml
        if(isset($file)) {
            $arr_file = explode('.', $originalFileName);
            $extension = end($arr_file);
            if('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } elseif ('xml' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xml();
            }
            $spreadsheet = $reader->load($file);
            $importProducts = $spreadsheet->getActiveSheet()->toArray();
        }
        //push products to datebase
        foreach ($importProducts as $importProduct) {
            $importProductId = $importProduct[0];
            $updatedId = $this->findProduct($products,$importProductId);
            if ($updatedId) {
                $product = $products[$updatedId];
                $product->setName($importProduct[3]);
                $product->setDescription($importProduct[4]);
                $product->setCount($importProduct[5]);
                $product->setPrice($importProduct[6]);
                $product->setCategory($importProduct[1]);
            }
            else {
                $product = new Product();
                $product->setName($importProduct[3]);
                $product->setDescription($importProduct[4]);
                $product->setCount($importProduct[5]);
                $product->setPrice($importProduct[6]);
                $product->setCategory($importProduct[1]);
                $product->setExternalId($importProduct[0]);
            }
            $entityManager->persist($product);
            $entityManager->flush();
        }
        return $importProducts;
    }
    private function findProduct($products, $id) {
        foreach ($products as $product) {
            if ($id == $product->getExternalId()){
                return $product->getId();
            }
        }
        return 0;
    }

}