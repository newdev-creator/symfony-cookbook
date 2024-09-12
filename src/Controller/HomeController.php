<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'home_index')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        // Get 3 random recipe with max rate
        // Find max rate on bdd
        $maxRate = $recipeRepository->findMaxRate();
        
        $recipes = [];

        // Stock 3 recipes with rate equal to $maxRate
        if ($maxRate !== null) {
            $recipes = $recipeRepository->findRandomRecipeByRate($maxRate, 3);

            // if $recipes[] dont full, get other recipes with $maxRate - 1
            if (count($recipes) < 3) {
                for ($rate=$maxRate - 1; $rate >= 1 && count($recipes) < 3; $rate--) { 
                    $addRecipes = $recipeRepository->findRandomRecipeByRate($rate, 3 - count($recipes));
                    $recipes = array_merge($recipes, $addRecipes);
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
}
