<h1>Room</h1>

<table border="1px" width="600px">
    <tr>
        <th>Room ID</th>
        <th>Temp 1</th>
        <th>Temp 2</th>
        <th>Temp 3</th>
        <th>Date : Time</th>
    </tr>

    <?php foreach ($rooms as $room) : ?>
        <tr align="center">
            <td>
                <?= $room->room_id ?>
            </td>
            <td>
                <?= $room->temp1 ?>
            </td>
            <td>
                <?= $room->temp2 ?>
            </td>
            <td>
                <?= $room->temp3 ?>
            </td>
            <td>
                <?= $room->datesave ?> :
                <?= $room->timesave ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>