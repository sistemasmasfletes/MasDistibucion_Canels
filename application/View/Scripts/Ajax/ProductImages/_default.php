<?php
$images = $view->images;
if ($images != false)
{
    echo "<table>";
    foreach ($images as $i)
    {
        echo "<tr>";
        echo "<td>";
        echo $view->ManagerImages()->thumbImg(
                $i->getPath(), '', '', '', '205', '120'
        );
        echo "</td>";
        echo "<td>";
        echo "<button class=\"ui-state-default ui-corner-all\" title=\"Eliminar\" onclick=\"deleteImage(".$i->getId().")\">";
        echo "<span class=\"ui-icon ui-icon-trash\">Eliminar<\/span>";
        echo "<\/button>";
        echo "</td>";
        echo "</tr>";
    }
     echo "</table>";
}
?>