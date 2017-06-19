<?php

/**
 * @file
 * This script writes the favourite recipes from cookipedia.co.uk into recipes.csv.
 */

require_once __DIR__ . '/../../../../../../vendor/autoload.php';

$recipes_fh = fopen('recipes.csv', 'w');

$http_client = new \GuzzleHttp\Client();

$client = new \Goutte\Client();
function fetchFields(\Goutte\Client $client, $url) {
  $client->request('GET', $url);
  /** @var \Symfony\Component\DomCrawler\Crawler $crawler */
  $crawler = $client->getCrawler();

  $fields = [];
  if ($schema_org_json = $crawler->filterXPath('//script[@type="application/ld+json"]')->text()) {
    $data = json_decode($schema_org_json, TRUE);
    // title,image,summary,author,category,preparation_time,total_time,difficulty,ingredients,recipe_instruction,number_of_servings,tags,recipe_review
    $fields[] = $data['name'];
    $fields[] = $data['image'];
    $fields[] = $data['description'];
    $fields[] = $data['author']['name'];

    if (strpos($data['recipeCategory'], 'Starter') !== FALSE) {
      $category = 'Starter';
    }
    elseif (strpos($data['recipeCategory'], 'Snack') !== FALSE) {
      $category = 'Snack';
    }
    elseif (strpos($data['recipeCategory'], 'Salad') !== FALSE) {
      $category = 'Salad';
    }
    else {
      $category = 'Main course';
    }

    $tags = array_map('trim', explode(',', $data['recipeCategory']));
    $tags = array_filter($tags, function ($tag) {
      return !in_array($tag, ['Main course', 'Salad', 'Snack', 'Starter']);
    });
    $tags[] = $data['recipeCuisine'];

    $fields[] = $category;

    try {
      $fields[] = isset($data['prepTime']) ? (new DateInterval($data['prepTime']))->i : NULL;
    }
    catch (\Exception $e) {
      $fields[] = NULL;
    }
    try {
      $fields[] = isset($data['totalTime']) ? (new DateInterval($data['totalTime']))->i : NULL;
    }
    catch (\Exception $e) {
      $fields[] = NULL;
    }

    // Extract the difficulty.
    $difficulty = NULL;
    if ($crawler->filterXPath('//div[contains(@class, "right_imgs")]//a[@href="/recipes_wiki/File:1o5dots.png"]')->count() > 0) {
      $difficulty = 'easy';
    }
    elseif ($crawler->filterXPath('//div[contains(@class, "right_imgs")]//a[@href="/recipes_wiki/File:2o5dots.png"]')->count() > 0) {
      $difficulty = 'middle';
    }
    elseif ($crawler->filterXPath('//div[contains(@class, "right_imgs")]//a[@href="/recipes_wiki/File:3o5dots.png"]')->count() > 0) {
      $difficulty = 'hard';
    }
    elseif ($crawler->filterXPath('//div[contains(@class, "right_imgs")]//a[@href="/recipes_wiki/File:4o5dots.png"]')->count() > 0) {
      $difficulty = 'hard';
    }
    elseif ($crawler->filterXPath('//div[contains(@class, "right_imgs")]//a[@href="/recipes_wiki/File:5o5dots.png"]')->count() > 0) {
      $difficulty = 'hard';
    }
    $fields[] = $difficulty;

    $fields[] = isset($data['recipeIngredient']) ? implode(',', $data['recipeIngredient']) : NULL;
    $fields[] = isset($data['recipeInstructions']) ? implode(',', $data['recipeInstructions']) : NULL;
  
    if (isset($data['servingSize'])) {
      $servingSizeString = $data['servingSize'];
      preg_match('/(\d+)/', $servingSizeString, $match);
      $fields[] = $match[1];
    }
    else {
      $fields[] = 0;
    }
    $fields[] = implode(',', $tags);
    $fields[] = NULL;
  }

  return $fields;
}

// Get all favourite recipes.
$client->request('GET', 'https://www.cookipedia.co.uk/recipes_wiki/Category:Favourite_recipes');
foreach ($client->getCrawler()->filter('div.mw-category-group li a')->getIterator() as $link) {
  /** @var \DOMElement $link */
  $url = 'https://www.cookipedia.co.uk' . $link->getAttribute('href');
  if ($fields = fetchFields($client, $url)) {
    fputcsv($recipes_fh, $fields);
  }
}

fclose($recipes_fh);
