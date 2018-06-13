    <nav class="navbar navbar-default">

      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="http://megusta.moe/mg-dl/">MG-DL</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li class="active"><a href="http://megusta.moe/mg411/">Accueil</a></li>
<!--          <li><a href="./series.php">Séries</a></li>-->
          <!--<li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Top <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="http://megusta.moe/mg411/top/today.php">Jour</a></li>
              <li><a href="http://megusta.moe/mg411/top/week.php">Semaine</a></li>
              <li><a href="http://megusta.moe/mg411/top/month.php">Mois</a></li>
            </ul>
          </li>-->
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <form action="http://megusta.moe/mg411/index.php" method="get" class="navbar-form navbar-left" role="search">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="<?php echo isset($search) ? $search : 'Rechercher un torrent'; ?>" value="<?php echo isset($search) ? $search : null; ?>" required>
              <div class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
              </div>
            </div>
          </form>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Utilisateur<span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="text-center">
                <span class="label label-success"><span class="glyphicon glyphicon-arrow-down"></span> <?php echo $_COOKIE['downloaded']; ?></span>
                <span class="label label-danger"><span class="glyphicon glyphicon-arrow-up"></span> <?php echo $_COOKIE['uploaded']; ?></span>
              </li>
<!--
              <li role="separator" class="divider"></li>
              <li><a href="http://megusta.moe/mg411/seedbox.php"><span class="glyphicon glyphicon-wrench"></span> Seedbox</a></li>
              <li><a href="http://megusta.moe/mg411/suivi.php"><span class="glyphicon glyphicon-star"></span> Mes séries</a></li>
              <li><a href="./downloads.php"><span class="glyphicon glyphicon-download"></span> Téléchargements</a></li>
              <li role="separator" class="divider"></li>
              <li><a href="./logout.php"><span class="glyphicon glyphicon-log-out"></span> Déconnexion</a></li>
            </ul>
-->
          </li>
        </ul>
      </div>

    </nav>
