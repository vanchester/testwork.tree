<?php

/**
 * Class MyTree
 * @about Класс для работы с деревом объектов Node
 * @author Сутурин Иван
 */
class MyTree extends Tree
{
	/**
	 * @var array Дерево отношений объектов
	 */
	private $_structure = array();

	/**
	 * @var Node[] Массив: имя узла => объект ветки
	 */
	private $_nodes = array();

	/**
	 * @var Node[] Таблица ссылок на элементы дерева
	 */
	private $_nodeLinks = array();

	/**
	 * Создает узел
	 * @param Node $node Ветка
	 * @param Node|null $parentNode Родительская ветка, Null - корень
	 * @throws Exception
	 */
	public function createNode(Node $node, $parentNode = null)
	{
		$nodeName = $node->getName();
		$this->_nodes[$nodeName] = $node;

		if ($parentNode instanceof Node) {
			$parentNodeName = $parentNode->getName();
			if (!array_key_exists($parentNodeName, $this->_nodeLinks)) {
				throw new Exception("Parent node {$parentNodeName} not found");
			}

			$structureLink = &$this->_nodeLinks[$parentNodeName];
			$structureLink[$nodeName] = '';

			$this->_nodeLinks[$nodeName] = &$structureLink[$nodeName];
			return;
		}

		$this->_structure = [$nodeName => []];
		$this->_nodeLinks[$nodeName] = &$this->_structure[$nodeName];
	}

	/**
	 * Удаляет узел и все дочерние узлы
	 * @param Node $node
	 */
	public function deleteNode(Node $node)
	{
		$this->_searchAndDeleteNodeRecursive($node->getName(), $this->_structure);
	}

	/**
	 * Поиск узла и удаление с очисткой всех связанных элементов
	 * @param string $deleteNodeName Название удаляемого узла
	 * @param array $structure Массив связей узлов
	 */
	private function _searchAndDeleteNodeRecursive($deleteNodeName, array &$structure)
	{
		if (array_key_exists($deleteNodeName, $structure)) {
			$this->_nodeGarbageCollector($structure);

			unset($structure[$deleteNodeName]);
			unset($this->_nodes[$deleteNodeName]);
			unset($this->_nodeLinks[$deleteNodeName]);

			return;
		}

		foreach($structure as &$item) {
			if (!is_array($item)) {
				continue;
			}

			if (!isset($item[$deleteNodeName])) {
				$this->_searchAndDeleteNodeRecursive($deleteNodeName, $item);
			} elseif (is_array($item[$deleteNodeName])) {
				$this->_nodeGarbageCollector($item[$deleteNodeName]);

				unset($item[$deleteNodeName]);
				unset($this->_nodes[$deleteNodeName]);
				unset($this->_nodeLinks[$deleteNodeName]);
				break;
			}
		}
	}

	/**
	 * Убирает данные, связанные с удаляемыми элементами
	 * @param array $nodes Массив узлов для удаления
	 */
	private function _nodeGarbageCollector(array $nodes)
	{
		foreach ((array)$nodes as $key => $nodeData) {
			unset($this->_nodes[$key]);
			unset($this->_nodeLinks[$key]);

			if (!is_array($nodeData)) {
				continue;
			}

			$this->_nodeGarbageCollector($nodeData);
		}
	}

	/**
	 * Делает узел $node дочерним по отношению к узлу $parent
	 * @param Node $node
	 * @param Node $parent
	 */
	public function attachNode(Node $node, Node $parent)
	{
		$nodeName = $node->getName();
		if (!array_key_exists($nodeName, $this->_nodeLinks)) {
			throw new \Utils\BrandModelCatalog\Exception("Node {$nodeName} not found");
		}

		$parentNodeName = $parent->getName();
		if (!array_key_exists($parentNodeName, $this->_nodeLinks)) {
			throw new \Utils\BrandModelCatalog\Exception("Node {$parentNodeName} not found");
		}

		$nodeToMove = [$nodeName => $this->_nodeLinks[$nodeName]];
		$this->deleteNode($node);

		$this->_nodeLinks[$parentNodeName] = $nodeToMove;
	}

	/**
	 * Получает узел по названию
	 * @param string $nodeName Название узла
	 * @return Node Узел
	 */
	public function getNode($nodeName)
	{
		return array_key_exists($nodeName, $this->_nodes) ? $this->_nodes[$nodeName] : null;
	}

	/**
	 * Возвращает дерево со всеми элементами в виде ассоциативного массива
	 * @return array
	 */
	public function export()
	{
		return $this->_structure;
	}
}
