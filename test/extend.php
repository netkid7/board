<?php
class Entree
{
    private $name;
    protected $ingredients = array();

    public function getName()
    {
        return $this->name;
    }

    public function __construct($name, $ingredients)
    {
        if (!is_array($ingredients)) {
            throw new Exception('$ingredients 가 배열이 아닙니다.');
        }

        $this->name = $name;
        $this->ingredients = $ingredients;
    }

    public function hasIngredient($ingredients)
    {
        return in_array($ingredients, $this->ingredients);
    }
}

class ComboMeal extends Entree 
{
    public function __construct($name, $entrees)
    {
        parent::__construct($name, $entrees);
        foreach ($entrees as $entree) {
            if (! $entree instanceof Entree) {
                throw new Exception('$entrees 의 원소는 객체여야 합니다.');
            }
        }
    }

    public function hasIngredient($ingredient)
    {
        foreach ($this->ingredients as $entree) {
            if ($entree->hasIngredient($ingredient)) {
                return true;
            } else {
                return false;
            }
        }
    }
}

// Run
try {
    $drink = new Entree('우유 한잔', '우유');
    if ($drink->hasIngredient('우유')) {
        print "맛있어!".PHP_EOL;
    }
} catch (Exception $e) {
    print "음료를 준비할 수 없습니다: ". $e->getMessage();
}

$soup = new Entree('닭고기 수프', array('닭고기', '물'));
$sandwich = new Entree('닭고기 샌드위치', array('닭고기', '빵'));
$combo = new ComboMeal('수프 + 샌드위치', array($soup, $sandwich));

foreach(['닭고기', '레몬', '빵', '물'] as $ing) {
    if ($soup->hasIngredient($ing)) {
        print "수프의 재료: $ing <br>";
    }

    if ($sandwich->hasIngredient($ing)) {
        print "샌드위치의 재료: $ing <br>";
    }
}

foreach (['닭고기', '물', '피클'] as $ing) {
    if ($combo->hasIngredient($ing)) {
        print "세트 메뉴에 들어가는 재료: $ing <br>";
    }
}

echo '<pre>';
// print_r($soup->ingredients);
// print_r($combo->ingredients);
echo '</pre>';