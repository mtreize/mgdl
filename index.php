<?php 
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require_once('Ygg.php');  
//   require_once('login_ygg.php'); 
  require_once('mg_utils.php'); 


  if(isset($_GET['search'])){
    $search=$_GET['search'];
    $results= searchByTerme($search);
  } 
  if(isset($_GET['download_url'])){
    
    $dl= download($_GET['download_url']);
  } 
?>

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <title>MG-DL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
  </head>
  <body>

  <div class="container">
<?php require_once __DIR__ . '/' . 'navbar.php'; ?>

<!--     <ol class="breadcrumb">
      <li><a href="http://megusta.moe/mg411/">Recherche</a></li>
      <li><a></a></li>
    </ol> -->
    
    <?php 
      
      //krumo($search);

    ?>

    <div class="row ">
      <div class="col-md-12">
        <div class="well">
          <form action="./index.php" method="get">
            <div class="col-md-8">
              <input type="text" class="form-control" name="search" placeholder="Rechercher un torrent" value="<?php if(isset($search)){echo $search;}?>" required autofocus>
            </div>


            <div class="col-md-4 po">
              <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Rechercher</button>
            </div>
          </form>
          <br/>
          <br/>
        </div>
      </div>
    </div>

    
    
    
    
    
    
<?php if(empty($search)) { ?>

    <div class="jumbotron text-center">
      <h2 class="text-primary">BACK IN THE GAME</h2>
      <p>Rechercher un torrent, et ajouter le directement à Plex.</p>
    </div>

<?php } else {

  if (empty($results)) { ?>

      <div class="jumbotron">
        <h1><small style="color:red">NOPE !</small></h1>
        <p>La recherche n'a retourné aucun résultat.</p>
        <p class="text-danger"><?php echo $results?></p>
      </div>

  <?php } else {  ?>

      <div class="table-responsive">
        <table id="results_table" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th class="textcentered">Nom</th>
              <th class="textcentered">DL</th>
              <th class="textcentered">Age</th>
              <th class="textcentered">Taille</th>
              <th class="textcentered">Complété</th>
              <th class="textcentered">Seeders</th>
              <th class="textcentered">Leechers</th>
            </tr>
          </thead>
          <tbody>
<?php 

    foreach ($results as $torrent) {
        echo '<tr>';
        echo '<td><a href="' . $torrent['href'] . '">' . $torrent['name'] . '</a></td>';
        echo '<td><a href="./index.php?download_url=' . $torrent['href'] . '">DOWNLOAD</a></td>';
        echo '<td>' . $torrent['age'] . '</td>';
        echo '<td>' . $torrent['size'] . '</td>';
        echo '<td>' . $torrent['completed'] . '</td>';
        echo '<td>' . $torrent['seeds'] . '</td>';
        echo '<td>' . $torrent['leechs'] . '</td>';
        echo '</tr>';
    } ?>
          </tbody>
        </table>
      </div>
  <?php
  } 
}

?>




  </div>
  <script src="./js/jquery.min.js"></script>
  <script src="./js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
  <script type="text/javascript">

    
    
    
    
      $(document).ready( function () {
        $('#results_table').DataTable({
            lengthChange: false,
            searching:false,
            paging: true,
            pageLength: 20,
            "language": {
                "zeroRecords": "Aucun résultat - désolé",
                "info": '<small class="text-muted">Page _PAGE_ sur _PAGES_ </small>',
                "infoEmpty": "Aucun résultats",
                "infoFiltered": "(filtrés sur _MAX_ au total)"
            }
        });
      } );
  </script>
</body>
</html>
