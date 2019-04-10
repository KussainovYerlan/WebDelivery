<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //add Category
        $category = new Category();
        $category->setName('Мясо');
        $manager->persist($category);
        $category = new Category();
        $category->setName('Фрукты и овощи');
        $manager->persist($category);
        $category->setName('Напитки');
        $manager->persist($category);
        $category->setName('Молочные продукты');
        $manager->persist($category);

        $manager->flush();

        //
        //
        //Нужно добавить Seller()!
        //
        //

        //add Products

        $categoryRepository = $manager->getRepository(Category::class);
        //add milk products
        $category = $categoryRepository->findOneBy(['name' => 'Молочные продукты']);
        $product = new Product();
        $product->setName('Молоко');
        $product->setCategory($category);
        $product->setCount(10);
        $product->setDescription('Вкусное и свежее молоко с проверенных ферм, 100% натуральное');
        $product->setPrice(50);
        $product->setExternalId(1);
        $product->setSeller();
        $manager->persist($product);

        $product = new Product();
        $product->setName('Сыр');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Натуральный сыр  не содержит красителей, зато богат кальцием и обладает выверенной консистенцией.');
        $product->setPrice(550);
        $product->setExternalId(2);
        $product->setSeller();
        $manager->persist($product);

        $product = new Product();
        $product->setName('Сливки');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Сливки это продукт, созданный из 100% натурального коровьего молока.');
        $product->setPrice(80);
        $product->setExternalId(3);
        $product->setSeller();
        $manager->persist($product);

        //add Напитки
        $category = $categoryRepository->findOneBy(['name' => 'Напитки']);
        $product = new Product();
        $product->setName('Вода');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Кристальная вода чистейших источников Алтая.');
        $product->setPrice(35);
        $product->setExternalId(4);
        $product->setSeller();
        $manager->persist($product);

        $product = new Product();
        $product->setName('Лимонад');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Классические лимонады на основе артезианской воды');
        $product->setPrice(65);
        $product->setExternalId(5);
        $product->setSeller();
        $manager->persist($product);

        //add fruit and vegetables
        $category = $categoryRepository->findOneBy(['name' => 'Фрукты и овощи']);
        $product = new Product();
        $product->setName('Яблоки');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Обладают очень сладким и гармоничным вкусом и великолепным ароматом.');
        $product->setPrice(96);
        $product->setExternalId(6);
        $product->setSeller();
        $manager->persist($product);

        $product = new Product();
        $product->setName('Бананы');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Бананы являются одной из древнейших пищевых культур..');
        $product->setPrice(58);
        $product->setExternalId(7);
        $product->setSeller();
        $manager->persist($product);

        $product = new Product();
        $product->setName('Томат');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Имеют отличный вкус и способствуют укреплению здоровья.');
        $product->setPrice(220);
        $product->setExternalId(8);
        $product->setSeller();
        $manager->persist($product);

        $category = $categoryRepository->findOneBy(['name' => 'Мясо']);

        $product = new Product();
        $product->setName('Курица');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Нежное куриное филе подходит для приготовления супов, салатов.');
        $product->setPrice(190);
        $product->setExternalId(9);
        $product->setSeller();
        $manager->persist($product);

        $product = new Product();
        $product->setName('Говядина');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Исключительно свежее мясо, которое полностью отвечает требованиям «халяль».');
        $product->setPrice(550);
        $product->setExternalId(10);
        $product->setSeller();
        $manager->persist($product);

        $product = new Product();
        $product->setName('Свинина');
        $product->setCategory($category);
        $product->setCount(20);
        $product->setDescription('Идеальный вариант для супов, гриля, домашних копченостей и закусок.');
        $product->setPrice(270);
        $product->setExternalId(11);
        $product->setSeller();
        $manager->persist($product);



        $manager->flush();


    }
}
