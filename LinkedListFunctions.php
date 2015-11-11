<?php

class LinkedList {

	public $list;
	public $items;
	public $number_of_items;
	public $root;
	public $head;
	public $tail;

	function __construct($list) {
		$this->list = $list;
		$this->head = $this->GetHead($this->list);
		$this->tail = $this->GetTail($this->list);
		$this->list = $this->TraverseList();
		$this->items = $this->GetItems();
		$this->number_of_items = $this->GetNumberOfItems();
	}

	function GetTail($temp_list) {
		// the tail of the list is the item value that isn't a key of any other item (i.e. it points to nobody)
		foreach ($temp_list as $key => $value) {
			if (! isset($temp_list[$value])) return $value;
		}
		return '';
	}

	function GetHead($temp_list) {
		// the head of the list is the key that isn't any other item's value (i.e. nobody points to it)
		foreach ($temp_list as $key => $value) {
			$this_key_in_any_other_value = 0;
			foreach ($temp_list as $subkey => $subvalue) {
				if ($key == $subvalue) {
					$this_key_in_any_other_value = 1;
					break;
				}
			}
			if ($this_key_in_any_other_value == 0) return $key;
		}
		return '';
	}

	function GetItems() {
		$temp_items = array();
		foreach ($this->list as $key => $value) {
			if (! $key && ! $value) continue;
			$temp_items[$value] = $key;
		}
		$temp_items[] = $this->tail;  // add tail
		return $temp_items;
	}

	function GetNumberOfItems() {
		return count($this->list) + 1;
	}

	function GetItemPosition($item) {
		$position = 0;
		foreach ($this->list as $key => $value) {
			$position++;
			if ($key == $item) return $position;
			if ($value == $position) return $position + 1;
		}
	}

	function PruneList($item) {
		$pruned_list = array();
		foreach ($this->list as $key => $value) {
			if ($key == $item) break; // stop once reach cut point
			$pruned_list[$key] = $value;
			if ($value == $item) $pruned_list[$key] = ''; // remove pointer to cut point
		}
		return $pruned_list;
	}

	function RemoveItem($cut_item) {
		$newlist = array();
		foreach ($this->list as $key => $value) {
			if ($value == $cut_item) $prior_item = $key;
			if ($key == $cut_item) {
				$after_item = $value;
				continue; // skip cut point
			}
			$newlist[$key] = $value;
		}
		if ($prior_item) $newlist[$prior_item] = $after_item; // change pointer of prior item to point to item after cut point
		if ($cut_item == $this->tail && $prior_item) $newlist[$prior_item] = ""; // erase pointer to tail if tail is cut point
		return $newlist;
	}

	function InsertItem($new_item, $position) {
		$newlist = array();
		$i = 0;
		foreach ($this->list as $key => $value) {
			$i++;
			if ($i == 1 && $position == 1) $after_item = $key;
			if ($i == $position - 1) {
				$after_item = $value;
				$newlist[$key] = $new_item;
				continue;
			}
			if ($i == $position) $newlist[$new_item] = $after_item;
			$newlist[$key] = $value;
		}
		if ($position == $this->number_of_items) $newlist[$new_item] = $after_item; // add last item if inserting at last position
		return $newlist;
	}

	function TraverseList() {
		$traversed_list = array();
		$item = $this->head;
		while (true) {
			$traversed_list[$item] = $this->list[$item]; // add to result set
			$item = $this->list[$item]; // set item to next
			if ($item == $this->tail) break; // stop if reached tail
		}
		return $traversed_list;
	}

	function ReverseList() {
		$reversed_list = array();
		foreach ($this->list as $key => $value) {
			if (! $key && ! $value) continue;
			$reversed_list[$value] = $key;  // swap keys and values
		}
		return $reversed_list;
	}

	function AttachLists($traversed_list1, $traversed_list2) {
		$tail1 = $this->GetTail($traversed_list1);
		$head2 = $this->GetHead($traversed_list2);
		$combined_list = array_merge($traversed_list1, array($tail1 => $head2), $traversed_list2);
		return $combined_list;
	}

	function SortListByValue($descending) {
		$sorted_list = array();

		// sort items
		$temp_items = $this->items;
		if (! $descending) sort($temp_items);
		else rsort($temp_items);
	
		// "explode" simple array of values back into an associative array
		$i = 0;
		foreach($temp_items as $item) {
			$next_item = $temp_items[$i + 1];
			if ($next_item) $sorted_list[$item] = $next_item;
			$i++;
		}
		return $sorted_list;
	}

}


/*
Linked list looks like this:
	x -> b -> j -> m -> f -> d -> w -> q
*/

$l = new LinkedList(
	array(
		'b' => 'j',
		'd' => 'w',
		'f' => 'd',
		'j' => 'm',
		'm' => 'f',
		'x' => 'b',
		'w' => 'q'
	)
);

echo "Number of items: {$l->number_of_items}\n";
echo "Head: " . $l->head . "\n";
echo "Tail: " . $l->tail . "\n";
echo "Items: " . implode(" ", $l->items) . "\n";

echo "Position of item 'j': " . $l->GetItemPosition('j') . "\n";
echo "Position of item 'w': " . $l->GetItemPosition('w') . "\n";

$r = new LinkedList( $l->ReverseList() );
echo "Reversed Items: " . implode(" ", $r->items) . "\n";
echo "Reversed Head: " . $r->head . "\n";
echo "Reversed Tail: " . $r->tail . "\n";

$p = new LinkedList( $l->PruneList('j') );
echo "Items once list pruned at 'j': " . implode(" ", $p->items) . "\n";

$p2 = new LinkedList( $l->PruneList('w') );
echo "Items once list pruned at 'w': " . implode(" ", $p2->items) . "\n";

$d = new LinkedList( $l->RemoveItem('m') );
echo "Items once list had 'm' deleted: " . implode(" ", $d->items) . "\n";

$d2 = new LinkedList( $l->RemoveItem('x') );
echo "Items once list had 'x' deleted: " . implode(" ", $d2->items) . "\n";

$d3 = new LinkedList( $l->RemoveItem('q') );
echo "Items once list had 'q' deleted: " . implode(" ", $d3->items) . "\n";

$i = new LinkedList( $l->InsertItem('a', 1) );
echo "Items once list had 'a' inserted at position 1: " . implode(" ", $i->items) . "\n";

$i2 = new LinkedList( $l->InsertItem('a', 3) );
echo "Items once list had 'a' inserted at position 3: " . implode(" ", $i2->items) . "\n";

$i3 = new LinkedList( $l->InsertItem('a', 7) );
echo "Items once list had 'a' inserted at position 7: " . implode(" ", $i3->items) . "\n";

$i4 = new LinkedList( $l->InsertItem('a', 8) );
echo "Items once list had 'a' inserted at position 8: " . implode(" ", $i4->items) . "\n";

$l2 = new LinkedList(
	array(
		'#' => '%',
		'%' => '^',
		'^' => '*'
	)
);
echo "List to append: " . implode(" ", $l2->items) . "\n";
$l3 = new LinkedList( $l->AttachLists($l->list, $l2->list) );
echo "Combined list: " . implode(" ", $l3->items) . "\n";

$s = new LinkedList( $l->SortListByValue('') );
echo "List sorted by value: " . implode(" ", $s->items) . "\n";

$rs = new LinkedList( $l->SortListByValue('descending') );
echo "List reverse-sorted by value: " . implode(" ", $rs->items) . "\n";


?>
