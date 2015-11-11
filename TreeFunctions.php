<?php

/*

Tree Functions

Tree looks like this:
	     3
	   /   \
	  5     2
	 / \    /
	1   4  6

Preorder traversal of tree
	3 5 1 4 2 6
Postorder traversal of tree
	1 4 5 6 2 3
Inorder traversal of tree
	1 5 4 3 6 2
Level order traversal of tree
	3 5 2 1 4 6
Get height of tree (in edges)
	2
Get width of tree (in nodes)
	3
Get top of tree
	1 5 3 2
Get bottom of tree
	1 4 6
Get longest limb
	3 2 6
Get longest straight limb
	3 5 1
Get lowest common ancestor of 1,4
	5
Get lowest common ancestor of 1,2
	3
Get distance between nodes 1,6
	4
Get distance between nodes 1,4
	2
Get path between nodes 1,6
	5 3 2
Get path between nodes 2,6
	
Get path between nodes 1,6
	5
Get path between nodes 1,6
	5
Get node count
	6
Print
		   3
		  /  \
		  5  2
		 / \ /
		 1 4 6
Print after swapping nodes 5 and 2:
		   3
		  /  \
		  2  5
		 / / \
		 6 1 4
Print after pruning node 2:
		   3
		  /
		  5
		 / \
		 1 4

*/


class Tree {

	private $tree;
	private $root;

	function __construct($input_tree) {
		$this->tree = $input_tree;
		$this->root = $this->GetRoot();
	}

	private function FindParentNode($node) {
		foreach ($this->tree as $key => $subarray) {
			foreach ($subarray as $value) {
				if ($value == $node) return $key;
			}
		}
		return ''; // no parent (at root of tree)
	}

	private function IsNodeOnRight($node) {
		// returns 1 if node is on the right and 0 if node is on the left
		foreach ($this->tree as $key => $subarray) {
			foreach ($subarray as $subkey => $value) {
				if ($value == $node) return $subkey;
			}
		}
		return ''; // no parent (at root of tree)
	}

	private function GetRoot() {
		// find the key of the array that isn't referenced as an element in any other array; this is the root of the tree
		foreach ($this->tree as $key => $subarray) {
			$key_in_any_elements = false;
			foreach ($this->tree as $other_key => $other_subarray) {
				foreach ($other_subarray as $other_value) {
					if ($other_value == $key) {
						$key_in_any_elements = true;
						break;
					}
				}
			}
			if ($key_in_any_elements == false) return $key;
		}
		return '';
	}

	function PreorderTraverseTree() {
		$shown = array();
		$node = $this->root;  // start at root
		while (true) {
			if (! in_array($node,$shown)) $shown[] = $node;
			$left = $this->tree[$node][0];
			$right = $this->tree[$node][1];
			if ($left && ! in_array($left,$shown)) $node = $left;
			else if ($right && ! in_array($right,$shown)) $node = $right;
			else {
				$node = $this->FindParentNode($node);
				if ($node == '') break; // exit when back at root and both right and left have been shown
			}
		}
		return $shown;
	}

	function PostorderTraverseTree($node) {
		$shown = array();
		if (! $node) $node = $this->root;  // start at root if no arg supplied
		$temp_root = $node;
		while (true) {
			$left = $this->tree[$node][0];
			$right = $this->tree[$node][1];
			if ($left && in_array($left,$shown)) $left = '';
			if ($right && in_array($right,$shown)) $right = '';
			if (! $right && ! $left) {
				$shown[] = $node;
				if ($node == $temp_root) break;  // exit when root is shown
				$node = $this->FindParentNode($node);
			}
			if ($left) $node = $left;
			else if ($right) $node = $right;
		}
		return $shown;
	}

	function InorderTraverseTree() {
		$shown = array();
		$node = $this->root;  // start at root
		while (true) {
			$left = $this->tree[$node][0];
			$right = $this->tree[$node][1];
			if ($left && in_array($left,$shown)) $left = '';
			if ($right && in_array($right,$shown)) $right = '';
			if (! $right && ! $left) {
				if (! in_array($node,$shown)) $shown[] = $node;
				$node = $this->FindParentNode($node);
				if ($node == '') break; // exit when back at root and both right and left have been shown
			}
			else if (! $left && $right) {
				$shown[] = $node;
				$node = $right;
			}
			else if ($left) $node = $left;
			else if ($right) $node = $right;
		}
		return $shown;
	}

	private function GetNodeLateralPosition($node) {
		static $levels_in = 0; // increases as we move to the right as we traverse up tree
		static $levels_up = 1; // multiplies by 5 as we go up each level: 1, 5, 25, 125
		$parent = $this->FindParentNode($node);
		if ($parent == '') {
			$result = $levels_in;
			$levels_in = 0; // reset because static
			$levels_up = 1; // reset because static
			return $result;
		}
		if ($this->IsNodeOnRight($node)) {
			$levels_in = $levels_in + $levels_up;
		}
		$levels_up = $levels_up * 5;
		$node = $parent;
		return $this->GetNodeLateralPosition($node);
	}

	private function GroupTreeIntoLevels() {
		// store depth of every node in an array grouped by depth level
		$levels_array[0] = array($this->root);
		foreach ($this->tree as $key => $subarray) {
			foreach ($subarray as $value) {
				if (! $value) continue;
				$levels_deep = $this->GetNodeDepth($value);
				$levels_array[$levels_deep][] = $value;
			}
		}
		ksort($levels_array);  // sort levels array 0 to N
		return $levels_array;
	}

	function LevelorderTraverseTree() {
		$levels_array = $this->GroupTreeIntoLevels();
	
		// iterate each level, sorting items within each level based on lateral postion, and storing results
		$results = array();
		foreach ($levels_array as $key => $subarray) {
			$tempsubarray = array();
			foreach ($subarray as $subkey => $value) {
				$tempkey = $this->GetNodeLateralPosition($value);
				$tempsubarray[$tempkey] = $value;
			}
			ksort($tempsubarray);  // sort based on lateral position
			$results = array_merge($results, $tempsubarray);  // add sorted items to results array
		}
		return $results;
	}

	private function GetNodeDepth($node) {
		static $levels_deep = 0;
		$parent = $this->FindParentNode($node);
		if ($parent == '') {
			$result = $levels_deep;
			$levels_deep = 0; // reset because static
			return $result;
		}
		$levels_deep++;
		$node = $parent;
		return $this->GetNodeDepth($node);
	}

	private function GetNodeAncestors($node) {
		static $ancestors = array();
		$parent = $this->FindParentNode($node);
		if ($parent == '') {
			$result = array_reverse($ancestors);
			$ancestors = array(); // reset because static
			return $result;
		}
		$ancestors[] = $parent;
		$node = $parent;
		return $this->GetNodeAncestors($node);
	}

	private function GetNodeStraightAncestors($node) {
		static $straight_ancestors = array();
		static $limb_angle = -1;  // stores whether limb is slanting right (1) or left (0)
		$this_node_angle = $this->IsNodeOnRight($node);
		if ($limb_angle == -1) $limb_angle = $this_node_angle;  // set angle if not yet set
		$parent = $this->FindParentNode($node);
		if ($parent && $this->IsNodeOnRight($parent) != $this_node_angle) $parent = ''; // stop if parent slanted other direction
		if ($parent == '') {
			$result = array_reverse($straight_ancestors);
			$straight_ancestors = array(); // reset because static
			$right_angle = -1; // reset because static
			return $result;
		}
		$straight_ancestors[] = $parent;
		$node = $parent;
		return $this->GetNodeStraightAncestors($node);
	}

	function GetTreeDepth() {
		$deepest = -1;
		foreach ($this->tree as $key => $subarray) {
			foreach ($subarray as $value) {
				if (! $value) continue;
				$levels_deep = $this->GetNodeDepth($value);
				if ($levels_deep > $deepest) $deepest = $levels_deep;
			}
		}
		return $deepest;
	}

	function GetTreeWidth() {
		$levels_array = $this->GroupTreeIntoLevels($this->root, $this->tree);

		// iterate each level, count items in level, and find max width
		$max_width = -1;
		foreach ($levels_array as $subarray) {
			$items_on_level = count($subarray);
			$max_width = max($max_width, $items_on_level);
		}
		return $max_width;
	}

	function GetTreeTop() {
		$top = array();

		// get left side of top
		$node = $this->root;  // start at root
		while (true) {
			$top[] = $node;
			$left = $this->tree[$node][0];
			if ($left == '') break; // exit when reach end of left side of top
			$node = $left;
		}
		// reverse order of elements in left side of top
		$top = array_reverse($top);

		// get right side of top
		$node = $this->root;  // start at root
		while (true) {
			if ($node != $this->root) $top[] = $node;  // skip root because we already have it
			$right = $this->tree[$node][1];
			if ($right == '') break; // exit when reach end of right side of top
			$node = $right;
		}

		return $top;
	}

	function GetTreeBottom() {
		$bottom = array();
		foreach ($this->tree as $key => $subarray) {
			if (! $this->tree[$key][0] && ! $this->tree[$key][1]) $bottom[] = $key;
		}
		return $bottom;
	}

	function GetLongestLimb() {
		$longest_length = -1;
		$longest_limb = array();
		foreach ($this->tree as $key => $subarray) {
			foreach ($subarray as $value) {
				if (! $value) continue;
				$this_limb = array();
				$this_limb[] = $value;
				$ancestors = $this->GetNodeAncestors($value);
				$this_limb = array_merge($ancestors, $this_limb);
				$this_length = count($this_limb);
				if ($this_length > $longest_length) {
					$longest_length = $this_length;
					$longest_limb = $this_limb;
				}
			}
		}
		return $longest_limb;
	}

	function GetLongestStraightLimb() {
		$longest_length = -1;
		$longest_limb = array();
		foreach ($this->tree as $key => $subarray) {
			foreach ($subarray as $value) {
				if (! $value) continue;
				$this_limb = array();
				$this_limb[] = $value;
				$straight_ancestors = $this->GetNodeStraightAncestors($value);
				$this_limb = array_merge($straight_ancestors, $this_limb);
				$this_length = count($this_limb);
				if ($this_length > $longest_length) {
					$longest_length = $this_length;
					$longest_limb = $this_limb;
				}
			}
		}
		return $longest_limb;
	}

	function GetLowestCommonAncestor($node1, $node2) {
		$ancestors1 = $this->GetNodeAncestors($node1);
		$ancestors2 = $this->GetNodeAncestors($node2);
		$common_ancestors = array_intersect($ancestors1, $ancestors2);
		$num_common_ancestors = count($common_ancestors);
		return $common_ancestors[$num_common_ancestors - 1];
	}

	function GetDistanceBetweenNodes($node1, $node2) {
		$ancestors1 = $this->GetNodeAncestors($node1);
		$ancestors2 = $this->GetNodeAncestors($node2);
		$common_ancestors = array_intersect($ancestors1, $ancestors2);
		$length_route1 = count($ancestors1) + 1 - count($common_ancestors);
		$length_route2 = count($ancestors2) + 1 - count($common_ancestors);
		$distance = $length_route1 + $length_route2;
		if (in_array($node1, $ancestors2) || in_array($node2, $ancestors1)) $distance = abs($length_route1 - $length_route2); // if route is inside another
		return $distance;
	}

	function GetPathBetweenNodes($node1, $node2) {
		$path = array();
		$rawpath1 = $this->GetNodeAncestors($node1);
		$rawpath2 = $this->GetNodeAncestors($node2);
		$common_path = array_intersect($rawpath1, $rawpath2);
		$path1 = array_diff($rawpath1, $common_path);
		$path2 = array_diff($rawpath2, $common_path);
		$lowest_common_ancestor = $this->GetLowestCommonAncestor($node1, $node2);
		$path = array_merge($path1, array($lowest_common_ancestor), $path2);
		if (in_array($node1, $rawpath2) || in_array($node2, $rawpath1)) $path = array_diff($path1, $path2); // if route is inside another
		$path = array_diff($path, array($node1)); // remove node1 itself
		$path = array_diff($path, array($node2)); // remove node2 itself
		return $path;
	}

	function SwapNodes($node1, $node2) {
		// note that nodes to be swapped must have the same parent
		$newtree = $this->tree;
		$parent = $this->FindParentNode($node1);
		if ($this->IsNodeOnRight($node1)) {
			$newtree[$parent][0] = $node1;
			$newtree[$parent][1] = $node2;
		}
		else {
			$newtree[$parent][0] = $node2;
			$newtree[$parent][1] = $node1;
		}
		return $newtree;
	}

	function GetNodeCount() {
		//return count($this->tree);
		$node_count = 0;
		foreach ($this->tree as $key => $subarray) {
			if ($key) $node_count++;
		}
		return $node_count;
	}

	function PruneTree($node) {
		$newtree = $this->tree;
		$pruned_nodes = $this->PostorderTraverseTree($node);
		foreach ($pruned_nodes as $subnode) {
			$parent = $this->FindParentNode($subnode);
			if ($this->IsNodeOnRight($subnode)) $newtree[$parent][1] = '';
			else $newtree[$parent][0] = '';
		}
		// trim empty elements
		foreach ($newtree as $key => $subarray) {
			if ($subarray[0] == '' && $subarray[1] == '') unset($newtree[$key]);
		}
		return $newtree;
	}

	function PrintTree() {
		$levels_array = $this->GroupTreeIntoLevels();

		// iterate each level, sorting items within each level based on lateral postion, and printing results
		$level_counter = 0;
		foreach ($levels_array as $key => $subarray) {
			$tempsubarray = array();
			foreach ($subarray as $subkey => $value) {
				$tempkey = $this->GetNodeLateralPosition($value);
				$tempsubarray[$tempkey] = $value;
			}
			ksort($tempsubarray);  // sort based on lateral position
			$edges = '';
			foreach ($tempsubarray as $subkey => $value) {
				if ($value == $this->root) break;
				if ($this->IsNodeOnRight($value)) $edges[] = '\\';
				else $edges[] = '/';
			}
			$indent = count($levels_array) - $level_counter;
			$padding = str_pad('', $indent, ' ');
			if ($edges) echo $padding . implode($padding, $edges) . "\n";
			echo $padding . implode($padding, $tempsubarray) . "\n";
			$level_counter++;
		}
	}

}


// define tree

$tree = array();
$tree['1'] = array('','');
$tree['2'] = array('6','');
$tree['3'] = array('5','2');
$tree['4'] = array('','');
$tree['5'] = array('1','4');
$tree['6'] = array('','');

$t = new Tree($tree);

$t->PrintTree();

echo "Preorder Traversal: " . implode(" ", $t->PreorderTraverseTree()) . "\n";
echo "Postorder Traversal: " . implode(" ", $t->PostorderTraverseTree('')) . "\n";
echo "Inorder Traversal: " . implode(" ", $t->InorderTraverseTree()) . "\n";
echo "Level-order Traversal: " . implode(" ", $t->LevelorderTraverseTree()) . "\n";
echo "Tree height: " . $t->GetTreeDepth() . "\n";
echo "Tree width: " . $t->GetTreeWidth() . "\n";
echo "Tree top: " . implode(" ", $t->GetTreeTop()) . "\n";
echo "Tree bottom: " . implode(" ", $t->GetTreeBottom()) . "\n";
echo "Longest limb: " . implode(" ", $t->GetLongestLimb()) . "\n";
echo "Longest straight limb: " . implode(" ", $t->GetLongestStraightLimb()) . "\n";
echo "Lowest common ancestor of '1' and '4': " . $t->GetLowestCommonAncestor('1', '4') . "\n";
echo "Lowest common ancestor of '1' and '2': " . $t->GetLowestCommonAncestor('1', '2') . "\n";
echo "Lowest common ancestor of '2' and '6': " . $t->GetLowestCommonAncestor('2', '6') . "\n";
echo "Distance between nodes '1' and '6': " . $t->GetDistanceBetweenNodes('1', '6') . "\n";
echo "Distance between nodes '1' and '4': " . $t->GetDistanceBetweenNodes('1', '4') . "\n";
echo "Distance between nodes '2' and '6': " . $t->GetDistanceBetweenNodes('2', '6') . "\n";
echo "Distance between nodes '3' and '5': " . $t->GetDistanceBetweenNodes('3', '5') . "\n";
echo "Distance between nodes '5' and '6': " . $t->GetDistanceBetweenNodes('5', '6') . "\n";
echo "Path between nodes '1' and '6': " . implode(" ", $t->GetPathBetweenNodes('1', '6')) . "\n";
echo "Path between nodes '1' and '4': " . implode(" ", $t->GetPathBetweenNodes('1', '4')) . "\n";
echo "Path between nodes '2' and '6': " . implode(" ", $t->GetPathBetweenNodes('2', '6')) . "\n";
echo "Path between nodes '1' and '3': " . implode(" ", $t->GetPathBetweenNodes('1', '3')) . "\n";
echo "Node count: " . $t->GetNodeCount() . "\n";

echo "Tree with nodes '5' and '2' swapped:\n";
$tree2 = $t->SwapNodes('5', '2');
$t2 = new Tree($tree2);
$t2->PrintTree();

echo "Tree with node '5' pruned:\n";
$tree3 = $t->PruneTree('5');
$t3 = new Tree($tree3);
$t3->PrintTree();

echo "Tree with node '2' pruned:\n";
$tree4 = $t->PruneTree('2');
$t4 = new Tree($tree4);
$t4->PrintTree();

echo "Tree with node '4' pruned:\n";
$tree5 = $t->PruneTree('4');
$t5 = new Tree($tree5);
$t5->PrintTree();

?>
