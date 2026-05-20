<?php
     $getRXmedic = mysqli_query($conn, "SELECT medicine_id, medicine_name FROM medicines WHERE status='1' ORDER BY medicine_id DESC") or die(mysqli_error($conn));
        while($row=mysqli_fetch_object($getRXmedic)) {
?>
    <option value="<?=$row->medicine_id ?>" > <?= $row->medicine_name ?> </option>
<?php } ?>