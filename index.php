<?php
  require_once __DIR__ . "/vendor/autoload.php";
  $client = new MongoDB\Client('mongodb://127.0.0.1:27017');
  $collection = $client->dbforlab->dutties;

  function prepare_list($col)
  {
     global $collection;
     $data = $collection->distinct($col);
     foreach($data as $item){
         $res=$res."<option value=".$item.">".$item."</option>";
     }
     return $res;
  }

 $nurses = prepare_list('nurses');
 $departments = prepare_list('department');
 $wards = prepare_list('wards');
?> 
<!DOCTYPE HTML>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>ЛБ 2</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> 
  <script src="script.js"></script>
  <link href="style.css" rel="stylesheet">
 </head>
 <body>
  <main>
  <h1 style="margin-left: 50px;">Виберіть дію:</h1><br>
  <form action="" method="post" class="block">
   <h3>Палати по медсестрам</h3>
   <span>
    <select name="nurse">    
       <?php echo $nurses ?>
    </select>
    <input type="submit" value="Перелік палат обраної медсестри"/>
    <input type="hidden" name="opt" value="wards" />
    </span>
  </form>
  <form action="" method="post" class="block">
    <h3>Медсестри по відділенням</h3>
    <span>
    <select name="department">    
	<?php echo $departments ?>
    </select>
    <input type="submit" value="Медсестри обраного відділення"/>
    <input type="hidden" name="opt" value="nurses" />
    </span>
  </form>
  <form action="" method="post" class="block">
  <h3>Чергування в обрану зміну</h3>
    <span>
      <select name="department">
        <?php echo $departments ?>
      </select>
   <select name="shift">
     <option value="first">Перша зміна</option>
      <option value="second">Друга зміна</option>
      <option value="third">Третя зміна</option>
   </select>
    <input type="submit" value="Чергування"/>
    <input type="hidden" name="opt" value= "shifts"/>
    </span>
  </form>
  <?php
  if(!strcmp($_POST['opt'],"wards")){
      $nurse=$_POST['nurse'];

      $table = "<tr><th>Медсестра</th><th>$nurse</th></tr>";
      $cursor = $collection->find(["nurses"=> $nurse],["projection"=>[
        "_id"=>0,
       "wards" => 1
       ]
      ])->toArray();
      $data = [];
      foreach($cursor as $row)
      {
        foreach($row['wards'] as $item){
          if(!in_array($item, $data)){
            array_push($data, $item);
            $table=$table."<tr><td>Палата</td><td>".$item."</td></tr>";
          }
        }
      }
    echo <<<END
    <div class="block" id="report">
      <table id="myTable">
      <caption><b>Список палат медсестри</b></caption>
        $table;
      </table>
      </div>
    END;
  }
  elseif(!strcmp($_POST["opt"],"nurses")){
      $department=$_POST['department'];
      $table = "<tr><th>Медсестра</th></tr>";
      $cursor = $collection->find(["department"=> $department],
        ["projection"=>[
          "_id"=>0,
          "nurses" => 1
        ]
      ])->toArray();
      $data = [];
      foreach($cursor as $row)
      {
        foreach($row['nurses'] as $item){
          if(!in_array($item, $data)){
            array_push($data, $item);
          $table=$table."<tr><td>".$item."</td></tr>";
          }
        }
      }

    echo <<<END
    <div class="block" id="report">
      <table id="myTable" class="table_dark">
        <caption><b>Відділення $department</b></caption>
        $table
      </table>
    </div>
    END;
   }
  elseif(!strcmp($_POST['opt'],"shifts")){
    $shift=$_POST['shift'];
    $department = $_POST['department'];
    $cursor = $collection->find(["shift"=> $shift,
          "department"=>$department
          ],
          ["projection"=>[
            "_id"=>0,
            "department"=>0
          ]
        ])->toArray();
      $table = "<tr><th>Медсестра</th><th>Палата</th><th>Дата</th></tr>";
      foreach($cursor as $row)
      {
        foreach($row['wards'] as $i)
          foreach($row['nurses'] as $j){
            $table=$table."<tr><td>".$j."</td><td>".$i."</td><td>".$row['date']."</td></tr>";
          }
      }
      echo <<<END
      <div class="block" id="report">
      <table id="myTable" class="table_dark">
        <caption><b>Cмена $shift Відділення $department</b></caption>
        $table
      </table>
      </div>
      END;
  }
  if($_POST['opt']){
     $selector = 'input[value='.$_POST['opt'].']';
     echo <<<END
      <script>
        registerKey();
      </script>
     END;
  }
  ?>
 </main>
 </body>
</html>