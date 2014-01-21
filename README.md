Тестовое задание
================

Задача
------
Реализовать класс дерева, наследующийся от абстрактного Tree:
```
class Node
{
    private $name;
	function __construct($name)
	{
		$this->name = $name;
	}
}

abstract class Tree
{
	// создает узел (если $parentNode == NULL - корень)
	abstract protected function createNode(Node $node,$parentNode=NULL);

	// удаляет узел и все дочерние узлы
	abstract protected function deleteNode(Node $node);

	// один узел делает дочерним по отношению к другому
	abstract protected function attachNode(Node $node,Node $parent);

	// получает узел по названию
	abstract protected function getNode($nodeName);

	// преобразует дерево со всеми элементами в ассоциативный массив
	abstract protected function export();
}
```

Обеспечить выполнение следующего теста:
```
// 1. создать корень country
$tree->createNode(new Node('country'));

// 2. создать в нем узел kiev
$tree->createNode(new Node('kiev'), $tree->getNode('country'));

// 3. в узле kiev создать узел kremlin
$tree->createNode(new Node('kremlin'), $tree->getNode('kiev'));

// 4. в узле kremlin создать узел house
$tree->createNode(new Node('house'), $tree->getNode('kremlin'));

// 5. в узле kremlin создать узел tower
$tree->createNode(new Node('tower'), $tree->getNode('kremlin'));

// 4. в корневом узле создать узел moskow
$tree->createNode(new Node('moskow'), $tree->getNode('country'));

// 5. сделать узел kremlin дочерним узлом у moskow
$tree->attachNode($tree->getNode('kremlin'), $tree->getNode('moskow'));

// 6. в узле kiev создать узел maidan
$tree->createNode(new Node('maidan'), $tree->getNode('kiev'));

// 7. удалить узел kiev
$tree->deleteNode($tree->getNode('kiev'));

// 8. получить дерево в виде массива, сделать print_r
print_r($tree->export());
```

Результатом последнего пункта должен быть следующий вывод в STDOUT:
```
Array
(
    [country] => Array
        (
            [moskow] => Array
                (
                    [kremlin] => Array
                        (
                            [house] =>
                            [tower] =>
                        )

                )

        )
)
```

Системные требования
--------------------
* PHP >= 5.4

Запуск решения
--------------
```
path/to/php path/to/index.php
```

Примечания
----------
1. В классе Node есть приватная переменная, которой присваивается значение. При этом возможности получить данные не предусмотрено. В задании говорится только о создании дочернего для Tree класса, но как самое простое решение добавил метод getName() в класс Node;
2. В задании для получения веток используется метод $tree->getNode('moskow'). Это предполагает, что названия веток не могут совпадать, даже на разных уровнях (например, в разных странах не может быть городов с одним и тем же названием). Решение выполнено с учетом этого момента.
3. Старался сделать максимально простое и быстрое (в плане работы скрипта) решение
