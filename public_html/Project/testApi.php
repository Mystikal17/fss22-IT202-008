<?php
require(__DIR__ . "/../../partials/nav.php");
$result = [];
if (isset($_GET["symbol"])) {
    $data = ["function" => "GLOBAL_QUOTE", "symbol" => $_GET["symbol"], "datatype" => "json"];
    $endpoint = "https://footapi7.p.rapidapi.com/api/rankings/fifa";
    $headers = [
        "X-RapidAPI-Host: footapi7.p.rapidapi.com",
        "X-RapidAPI-Key: 7064fbb722msh96e1dfabe0a4b06p1c20cdjsn18926f2976e3"
    ];
    
    $options = [
        'http' => [
            'header' => implode("\r\n", $headers),
            'method' => 'GET',
            'content' => http_build_query($data),
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($endpoint, false, $context);
    
    if ($response === false) {
        echo "Failed to fetch data.";
    } else {
        $result = json_decode($response, true);
        // Handle the response and database insertion here
    }
}
?>

<div class="container-fluid">
    <h1>Stock Info</h1>
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Symbol</label>
            <input name="symbol" />
            <input type="submit" value="Fetch Stock" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $stock) : ?>
                <pre>
            <?php var_export($stock);
            ?>
            </pre>
                <table style="display: none">
                    <thead>
                        <?php foreach ($stock as $k => $v) : ?>
                            <td><?php se($k); ?></td>
                        <?php endforeach; ?>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($stock as $k => $v) : ?>
                                <td><?php se($v); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");