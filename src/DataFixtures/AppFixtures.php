<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Step;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Comment;
use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = [];
        $recipes = [];
        $ingredients = [];
        $categories = [];

        // INGREDIENT
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($faker->word());
            $ingredient->setCreatedAt(new \DateTimeImmutable());
            $ingredient->setUpdatedAt(new \DateTimeImmutable());
            $ingredients[] = $ingredient;

            $manager->persist($ingredient);
        }

        // USER
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setPseudo($faker->userName());
            $user->setEmail($faker->email());
            $user->setAvatar($faker->imageUrl(100, 100, 'people'));
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            // Hash password
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);
            $users[] = $user;

            $manager->persist($user);
        }

        // CATEGORY
        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($faker->word());
            $category->setImage($faker->imageUrl(640, 480, 'food'));
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setUpdatedAt(new \DateTimeImmutable());
            $categories[] = $category;

            $manager->persist($category);
        }

        // RECIPE
        for ($i = 0; $i < 30; $i++) {
            $recipe = new Recipe();
            $recipe->setTitle($faker->sentence());
            $recipe->setDescription($faker->paragraph());
            $recipe->setPersonCount($faker->numberBetween(1, 10));
            $recipe->setPrepTimeCount($faker->numberBetween(10, 60));
            $recipe->setCookTimeCount($faker->numberBetween(10, 120));
            $recipe->setRate($faker->numberBetween(1, 5));
            $recipe->setServing($faker->numberBetween(1, 10));
            $recipe->setImage($faker->imageUrl(640, 480, 'food'));
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());

            // Assign a random user
            $recipe->setOwner($faker->randomElement($users));
            $recipes[] = $recipe;

            // Assign a random category
            $assignedCategories = $faker->randomElements($categories, $faker->numberBetween(1, 10));
            foreach ($assignedCategories as $category) {
                $recipe->addCategory($category);
            }

            $manager->persist($recipe);
        }

        // STEP
        foreach ($recipes as $recipe) {
            for ($i = 1; $i <= 5; $i++) {
                $step = new Step();
                $step->setStepNumber($i);
                $step->setDescription($faker->paragraph());
                $step->setCreatedAt(new \DateTimeImmutable());
                $step->setUpdatedAt(new \DateTimeImmutable());
                $step->setRecipe($recipe);

                $manager->persist($step);
            }
        }

        // COMMENT
        foreach ($recipes as $recipe) {
            for ($i = 0; $i < 5; $i++) {
                $comment = new Comment();
                $comment->setDescription($faker->sentence());
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setUpdatedAt(new \DateTimeImmutable());
                $comment->setRecipe($recipe);
                $comment->setOwner($faker->randomElement($users));

                $manager->persist($comment);
            }
        }

        // RECIPE_INGREDIENT
        foreach ($recipes as $recipe) {
            for ($i = 0; $i < 5; $i++) {
                $recipeIngredient = new RecipeIngredient();
                $recipeIngredient->setRecipe($recipe);
                $recipeIngredient->setIngredient($faker->randomElement($ingredients));
                $recipeIngredient->setUnity('g'); // Example unit
                $recipeIngredient->setQuantity($faker->numberBetween(1, 500));

                $manager->persist($recipeIngredient);
            }
        }

        $manager->flush();
    }
}
