<?php


function getParentCategory($mysqli, $parent_id, $item=false) {

    $category_sql = "SELECT * FROM `category` WHERE parent_id = 0 AND deleted_at is Null";
    $category_result = $mysqli->query($category_sql);
    while ($category_row = $category_result->fetch_assoc()) {
        $disabled = '';
        $parent_cat_id = (int)($category_row['id']);
        $parent_cat_name = htmlspecialchars($category_row['name']);
        
        if($item) {
            $is_child_exit = checkChildCategoryExit($parent_cat_id, $mysqli);
            if($is_child_exit) {
                $disabled = 'disabled';
            }

        } else {
            $is_item_exist = checkItemExit($parent_cat_id, $mysqli);
            if($is_item_exist) {
                $disabled = 'disabled';
            }
        }

        if($parent_id == $parent_cat_id) {
            echo "<option value='$parent_cat_id' selected $disabled>$parent_cat_name</option>";
        } else {
            echo "<option value='$parent_cat_id' $disabled>$parent_cat_name</option>";
        }
        
        getChildCategory($parent_cat_id, 1, $parent_id, $item, $mysqli);
    }
}

function getChildCategory($parent_cat_id, $count, $parent_id, $item=false, $mysqli)
 {
    $child_cat_sql = "SELECT id, name FROM `category` WHERE parent_id = $parent_cat_id AND deleted_at is Null";
    $child_cat_result = $mysqli->query($child_cat_sql);

    while ($child_cat_row = $child_cat_result->fetch_assoc()) { 
        $disabled = '';
        $child_cat_id = $child_cat_row['id'];
        $child_cat_name = $child_cat_row['name'];
        $dash = str_repeat('-- ', $count); // Include a space after each pair of hyphens

        if($item) {
            $is_child_exit = checkChildCategoryExit($child_cat_id, $mysqli);
            // if($is_item_exist) {
            //     $disabled = 'disabled';
            // }
            if($is_child_exit) {
                $disabled = 'disabled';
            }
        } else {
            $is_item_exist = checkItemExit($child_cat_id, $mysqli); // Change here

            if ($is_item_exist) {
                $disabled = 'disabled';
            }
        }


        if($parent_id == $child_cat_id) {
            echo "<option value='$child_cat_id' selected> $disabled" . $dash . $child_cat_name . " </option>";
        } else {
            echo "<option value='$child_cat_id' $disabled>" . $dash . $child_cat_name . " </option>";
        }
        


        $is_child_cat_sql = "SELECT count(id) AS total FROM `category` WHERE parent_id = $child_cat_id AND deleted_at is Null";
        $is_child_cat_result = $mysqli->query($is_child_cat_sql);

        while ($is_child_cat_row = $is_child_cat_result->fetch_assoc()) { 
            $is_child_total = $is_child_cat_row['total'];
        }

        if($is_child_total > 0) {
            getChildCategory($child_cat_id, $count + 1, $parent_id,$item, $mysqli); // Increment count for deeper indentation
        }
    }   
}
function checkChildCategoryExit($parent_cat_id, $mysqli) {
    $total = 0;
    $sql = "SELECT count(id) AS total FROM category WHERE parent_id = '$parent_cat_id' and deleted_at IS NULL";
    $res = $mysqli->query($sql);
        while($row_cunt = $res->fetch_assoc())
        {
            $total = $row_cunt['total'];
        }
        return $total;
 }

 function checkItemExit($parent_cat_id, $mysqli){
    $total = 0;
    $item_sql = "SELECT count(id) AS total FROM `item` WHERE category_id = '$parent_cat_id' and deleted_at is Null";
    $item_res = $mysqli->query($item_sql);
    while($item_row_cunt = $item_res->fetch_assoc())
    {
        $total = $item_row_cunt['total'];
    }
    return $total;
 }
?>