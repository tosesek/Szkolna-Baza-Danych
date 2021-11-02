<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/panel/modules/header.php';
  cMustBeAdmin();

  $bytes_count = 0;
  $files_count = 0;
  $dirs_count = 0;

  $stats = array(
    "php" => array(
      "name" => "PHP",
      "extensions" => ["php", "tpl"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "html" => array(
      "name" => "HTML",
      "extensions" => ["html", "htm", "xhtml", "asp", "aspx", "dhtml", "phtml", "jhtml", "rhtml", "shtml", "zhtml"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "css" => array(
      "name" => "CSS",
      "extensions" => ["css", "scss"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "javascripts" => array(
      "name" => "JavaScript",
      "extensions" => ["js", "ts"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "images" => array(
      "name" => "Grafika rastrowa",
      "extensions" => ["raw", "ico", "pix", "matte", "mask", "alpha", "als", "bmp", "dds", "dcm", "dicom", "gif", "jpg", "jpeg", "jpe", "exr", "pbm", "pfm", "pgm", "png", "pnm", "ppm", "psd", "sgi", "rgb", "rgba", "bw", "icon", "im1", "im8", "im24", "im32", "rs", "ras", "tga", "tiff", "tif", "webp", "xbm", "bitmap", "xpm", "pcx", "pcc", "dib", "jfif", "heic"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "vectors" => array(
      "name" => "Grafika wektorowa",
      "extensions" => ["svg", "ydr", "wpi", "ai", "vsdx", "svgz", "cdr", "pobj", "dpr", "eps", "ep", "jsl", "ink", "fh9", "lmk", "wmf", "cddz", "cxf", "otg", "fh8", "pd", "pat", "cvx", "gvdesign", "scv", "fh7", "slddrt", "gdraw", "cdd", "cmx", "afdesign", "ps", "fh10", "cdrapp", "vstm", "af3", "dsf", "gstencil", "cil", "epsf", "drawit", "avif", "emz", "dpp", "pmg", "drw", "fxg", "igx", "idea", "ac6", "apg", "vsd", "vml", "cds", "pfd", "plt", "hpgl", "odg", "rdl", "wpg", "cdmz", "egc", "fh11", "mvg", "std", "vstx", "stn", "glox", "dia", "cdtx", "gsd", "ded", "sda", "fhd", "abc", "cvs", "vsdm", "emf", "sketchpad", "mgtx", "cv5", "fh4", "fh6", "sk1", "ft9", "ovr", "fif", "fh5", "dcs", "cvg", "csy", "mgcb", "sk2", "mgmx", "tne", "cdmtz", "drawing", "pen", "dhs", "snagstyles", "sxd", "ezdraw", "asy", "cvi", "tlc", "sketch", "fig", "mp", "graffle", "mgc", "vec", "clarify", "cnv", "svf", "esc", "ac5", "smf", "psid", "fmv", "cgm", "design", "pfv", "ufr", "ft10", "ovp", "dxb", "ft8", "fs", "wmz", "imd", "xmmap", "awg", "cdmm", "artb", "art", "hgl", "pl", "dpx", "zgm", "af2", "gtemplate", "puppet", "gem", "pdrw", "mgs", "hpl", "yal", "snagitstamps", "vst", "ft11", "amdn", "mmat", "ssk", "mgmt", "ccx", "xpr", "mgmf", "cor", "dsg", "cdmt", "ftn", "cdt", "qcc", "cdlx", "cdsx", "vbr", "pcs", "cwt", "cag", "gls", "igt", "cdx", "pws", "nap", "gks", "ft7", "fh3"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "fonts" => array(
      "name" => "Czcionki",
      "extensions" => ["jfproj", "mf", "ttf", "woff", "pfa", "fot", "fnt", "sfd", "vlw", "otf", "odttf", "gxf", "pfb", "etx", "chr", "vfb", "woff2", "bdf", "amfm", "pmt", "pfm", "compositefont", "gf", "gdr", "abf", "vnf", "fon", "acfm", "ttc", "mxf", "pcf", "sfp", "t65", "pfr", "xfn", "tfm", "glif", "dfont", "pk", "eot", "afm", "xft", "lwfn", "ffil", "suit", "mcf", "nftr", "txf", "tte", "cha", "ufo", "ytf", "f3f", "euf", "pf2", "fea"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "configs" => array(
      "name" => "Pliki konfiguracyjne",
      "extensions" => ["htaccess", "htpasswd", "conf", "ini", "cfg", "yml", "config", "properties"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "scripts" => array(
      "name" => "Skrypty",
      "extensions" => ["sh", "bat", "cmd", "vb", "vbs"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "database" => array(
      "name" => "Pliki przechowujące dane",
      "extensions" => ["sql", "map", "xml", "xaml", "json", "jsonp", "csv", "db"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "text" => array(
      "name" => "Pliki tekstowe",
      "extensions" => ["txt"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "pdf" => array(
      "name" => "PDF",
      "extensions" => ["pdf"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "archives" => array(
      "name" => "Archiwa",
      "extensions" => ["zip", "tar", "rar", "gz", "cab", "iso", "txz", "xz", "lzma", "cpio", "bz2", "bzip2", "tbz2", "tbz", "gzip", "tgz", "tpz", "7z", "z", "taz", "lzh", "lha", "rpm", "deb", "arj", "vhd", "wim", "swm", "fat", "ntfs", "dmg", "hfs", "xar", "squashfs", "001"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "videos" => array(
      "name" => "Filmy wideo",
      "extensions" => ["3g2", "3gp", "3gp2", "3gpp", "amv", "asf", "avi", "bik", "crf", "divx", "drc", "dv", "dvr-ms", "evo", "f4v", "flv", "gvi", "gxf", "m1v", "m2v", "m2t", "m2ts", "m4v", "mkv", "mov", "mp2", "mp2v", "mp4", "mp4v", "mpe", "mpeg", "mpeg1", "mpeg2", "mpeg4", "mpg", "mpv2", "mts", "mtv", "mxf", "mxg", "nsv", "nuv", "ogg", "ogm", "ogv", "ogx", "rec", "rm", "rmvb", "rpl", "thp", "tod", "tp", "tts", "txd", "vob", "vro", "webm", "wm", "wmv", "wtv", "xesc"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "audio" => array(
      "name" => "Pliki dźwiękowe",
      "extensions" => ["3ga", "669", "a52", "aac", "ac3", "adt", "adts", "aif", "aifc", "aiff", "amb", "amr", "aob", "ape", "au", "awb", "caf", "dts", "flac", "it", "kar", "m4a", "m4b", "m4p", "m5p", "mid", "midi", "mka", "mlp", "mod", "mpa", "mp1", "mp3", "mpc", "mpga", "mus", "oga", "oma", "opus", "qcp", "ra", "rmi", "s3m", "sid", "spx", "tak", "thd", "tta", "voc", "vqf", "w64", "wav", "wma", "wv", "xa", "xm", "m3u", "m3u8", "pls"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "binfiles" => array(
      "name" => "Pliki binarne",
      "extensions" => ["exe", "dll", "bin", "com", "ocx", "dat", "drv"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "cpp_c" => array(
      "name" => "Pliki C++ lub C#",
      "extensions" => ["cpp", "h", "cs", "c", "cmake"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "python" => array(
      "name" => "Python",
      "extensions" => ["py"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "java" => array(
      "name" => "Java",
      "extensions" => ["java", "jar", "class"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    ),
    "restricted" => array(
      "name" => "Zabronione pliki",
      "extensions" => ["dat_old", "lock", "mca", "skin", "player", "mcmeta", "vdf", "vpk", "vtf", "vmt", "bsp", "vmf", "dem", "vbsp", "nav", "kv", "vmx", "prt", "mdl", "vtx", "vvd", "phy", "3ds", "vfont", "nut"],
      "count" => 0,
      "bytes" => 0,
      "detailed" => array()
    )
  );

  $users_stats = array();

  $ite = new RecursiveDirectoryIterator($_SERVER['DOCUMENT_ROOT']);

  foreach(new RecursiveIteratorIterator($ite) as $filename => $cur) {
    if (stripos($filename, "var/www/html/panel") !== false) continue;
    if (substr($filename, -2, 2) == "..") continue;
    // if (substr($filename, -1, 1) == ".") continue;

    // /var/www/html/.
    // /var/www/html/index.php
    
    $userpath = explode("/", $filename)[4];
    if (!$users_stats[$userpath] && strpos($userpath, ".") === false && is_dir("/var/www/html/$userpath")) {
      $users_stats[$userpath] = array(
        "files" => 0,
        "name" => $userpath,
        "folders" => -1,
        "bytes" => 0
      );
    }

    

    if (substr($filename, -2) == "/.") { 
      $dirs_count++; 
      if(strpos($userpath, ".") === false && is_dir("/var/www/html/$userpath")) $users_stats[$userpath]['folders']++;
      continue;
    }
    
    if(strpos($userpath, ".") === false && is_dir("/var/www/html/$userpath") && !is_dir($filename)) $users_stats[$userpath]['files']++;
    if(!is_dir($filename)) $files_count++;
    


    $filesize = $cur->getSize();    
    if(strpos($userpath, ".") === false && is_dir("/var/www/html/$userpath") && !is_dir($filename)) $users_stats[$userpath]['bytes'] += $filesize;
    $bytes_count += $filesize;
    

    foreach ($stats as $key => $value) {
      $path_exploded = explode("/", $filename); 
      $fname = $path_exploded[sizeof($path_exploded)-1];
      $fname_exploded = explode(".", $fname);
      $extension = $fname_exploded[sizeof($fname_exploded)-1];
      
      if (in_array($extension, $value['extensions'])) {
        // if (!in_array($extension, $stats[$key]['detailed'])) $stats[$key]['detailed'][$extension] = 0;
        $stats[$key]["count"]++;
        $stats[$key]['detailed'][$extension]++;
        $stats[$key]['bytes'] += $cur->getSize();
        // break;
      }
      else {
        continue;
      }
    }    
  }

  // header('Content-Type: Application/json');
  // echo json_encode($users_stats, JSON_PRETTY_PRINT);
  // exit();

  $colors1 = [
    "#A11111", "#11A111", "#1111A1",      "#A55555", "#55A555", "#5555A5",      "#A99999", "#99A999", "#9999A9",
    "#B11111", "#11B111", "#1111B1",      "#B55555", "#55B555", "#5555B5",      "#B99999", "#99B999", "#9999B9",
    "#C11111", "#11C111", "#1111C1",      "#C55555", "#55C555", "#5555C5",      "#C99999", "#99C999", "#9999C9",
    "#D11111", "#11D111", "#1111D1",      "#D55555", "#55D555", "#5555D5",      "#D99999", "#99D999", "#9999D9",
    "#E11111", "#11E111", "#1111E1",      "#E55555", "#55E555", "#5555E5",      "#E99999", "#99E999", "#9999E9",
    "#F12312", "#12F312", "#1231F2",      "#F45645", "#45F645", "#4564F5",      "#F78978", "#78F978", "#7897F8",
    "#ABC123", "#BCA321", "#ACB132",      "#DEF456", "#FED654", "#DFE465",      "#AFD798", "#ACE916", "#EBC370",
    "#ffaacc", "#ccaaff", "#ffccaa",      "#facada", "#fbvbdb", "#fecede",      "#beceae", "#bcccac", "#CAFECD"
  ];

  $colors2 = [
    "#720e7c", "#3440e1", "#825789",      "#d54ceb", "#1f5aae", "#54c62e",      "#c3a3fc", "#326f42", "#02fe41",
    "#08cd83", "#1bcdc8", "#5476ec",      "#a5898f", "#833bc0", "#8655a8",      "#9dbdb0", "#be913b", "#8224d3",
    "#56c103", "#82fe0b", "#52bf34",      "#364c1c", "#76cb92", "#f9d6c5",      "#57e9e9", "#45523e", "#60b5ce",
    "#a529fd", "#09abeb", "#4b4b53",      "#0c05ae", "#75f4db", "#35bf66",      "#ad6ccb", "#001e57", "#fda123",
    "#251e1e", "#64f595", "#c5c657",      "#c7c931", "#b38b20", "#5bfe34",      "#f6f69d", "#f6acc2", "#2a2b42",
    "#7b08bb", "#f09f2e", "#12296d",      "#345afa", "#6788af", "#d95e44",      "#19488d", "#bac353", "#9a1c77",
    "#be9a22", "#04f477", "#00686c",      "#8bc5be", "#bcbc18", "#acf33f",      "#412d24", "#7253cc", "#6327de",
    "#247ed9", "#078190", "#b9b5e0",      "#7637cf", "#8bd443", "#7ce101",      "#0dc253", "#9eb27e", "#ce6549"
  ];

  $colors3 = [
    "#bfb63b", "#941509", "#4efbc0",      "#0d6288", "#dd0883", "#30c797",      "#b23aae", "#b17187", "#54d782",
    "#d9324d", "#991805", "#2bf5c0",      "#41224c", "#055e2d", "#1823d1",      "#961eca", "#d22f10", "#e28b6a",
    "#f9aed8", "#7bc0e4", "#227ec6",      "#da1e05", "#1de0ac", "#e1c41e",      "#34d755", "#55a69c", "#2c7b3d",
    "#db4416", "#5ce5e5", "#217f2b",      "#518ac6", "#8fc17f", "#eee87c",      "#e60b14", "#0189fd", "#a7dd29",
    "#ac0e10", "#10368d", "#90c5fc",      "#f38e08", "#c97c84", "#712c73",      "#5970d4", "#ea6577", "#f000e3",
    "#0a3195", "#558e6c", "#db44c0",      "#7e1fda", "#e85802", "#999791",      "#393261", "#06ddec", "#1bf55e",
    "#831631", "#71b8a7", "#1dd954",      "#e557c3", "#f70a36", "#df9dab",      "#80130a", "#a51932", "#ac9ad5",
    "#499734", "#c4956b", "#41fd83",      "#a8efe9", "#f8503c", "#0a4370",      "#f76a7b", "#bb654b", "#e24470"
  ];
  
  // $colors3 = ["Gold","SaddleBrown","Gray","Fuchsia","hotpink","MediumVioletRed","LightSeaGreen","Crimson","Firebrick","Red","CornflowerBlue","Tomato","DarkOrange","Yellow","Moccasin","Khaki","Wheat","RosyBrown","Goldenrod","Peru","Brown","Plum","MediumOrchid","BlueViolet","DarkOrchid","LightSteelBlue","Purple","Indigo","Gray","DarkSlateGray","Purple","Indigo","DarkSlateGray","DarkOliveGreen","OliveDrab","LightSalmon","LimeGreen","LawnGreen","Chartreuse","MediumTurquoise","SpringGreen","PaleVioletRed","LightGreen","DarkSeaGreen","MediumSeaGreen","ForestGreen","DarkGreen","Aqua","Aquamarine","LightCoral","DarkCyan","LightBlue","DeepSkyBlue","DodgerBlue","RoyalBlue","Blue","Lime","MidnightBlue"];  
  
?>

<div class="page_generic">
  <div class="content">
    <h1>Statystyki systemu</h1>
    <h5>Aktualne informacje dotyczące statystyk systemu</h5>
    <hr>

    <div class="admin-tools-stats">
      <span class="title">Użytkownicy</span>
      <div class="content">
        <div style="position: realtive; display: grid; grid-template-columns: auto 1fr; grid-gap: 25px; margin-bottom: 50px">
          <div style="position: relative; display: block; width: 600px;">
            <canvas id="classes_stats"></canvas>
            <script>
              <?php
                $uq = mysqli_query($_DB, "SELECT COUNT(`id`) as `count`, `class` FROM users WHERE NOT `class`='Reserved' GROUP BY `class` ORDER BY `class` ASC");

                $classes = array();
                $classes_counts = array();
                $classes_colors = array();

                $colorid = 0;

                while($r = mysqli_fetch_assoc($uq)) {
                  if ($r['count'] == 0) continue;
                  $classes[] = $r['class'];
                  $classes_counts[] = $r['count'];

                  $classes_colors[] = $colors1[$colorid];

                  $colorid++;
                }
              ?>

              var ctx = document.getElementById("classes_stats").getContext('2d');
              var overall_stats = new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: <?=json_encode($classes)?>,
                  datasets: [{
                    label: "Klasy",
                    data: <?=json_encode($classes_counts)?>,
                    backgroundColor: <?=json_encode($classes_colors)?>,
                    borderColor: <?=json_encode($classes_colors)?>
                  }]
                },
                options: {
                  responsive: true,
                  legend: {
                    display: true,
                    position: 'right'
                  },
                  beginAtZero: true,
                  stepSize: 100
                }
              });
            </script>
          </div>
          <div style="">
            <h1>Ogólne dane</h1>
            <!-- <div style="position: relative; display: block; padding: 15px; background: #00336670; box-shadow: inset 0px 0px 0px 1.5px #0af, 0px 0px 15px #0066aaa0; margin: 10px 0px; color: #fff; font: 16px YouTube Bold; border-radius: 5px">
              Pliki potrzebne do działania systemu zostały wykluczone ze statystyk.
            </div> -->
            <?php
              $current_accounts = mysqli_num_rows(mysqli_query($_DB, "SELECT * FROM users"));
              $verified_accounts = mysqli_num_rows(mysqli_query($_DB, "SELECT * FROM users WHERE `verified`=2"));
              $waiting_accounts = mysqli_num_rows(mysqli_query($_DB, "SELECT * FROM users WHERE `verified`<2"));
              $developer_accounts = mysqli_num_rows(mysqli_query($_DB, "SELECT * FROM users WHERE `developer`=1"));
              $admin_accounts = mysqli_num_rows(mysqli_query($_DB, "SELECT * FROM users WHERE `privileges`=1 AND `developer`=0"));
              $blocked_accounts = mysqli_num_rows(mysqli_query($_DB, "SELECT * FROM users WHERE `blocked`=1"));
            ?>
            Łączna ilość zapisanych kont: <b><?=$current_accounts?></b><br>
            <br>
            Ilość zweryfikowanych kont: <b><?=$verified_accounts?></b><br>
            Ilość oczekujących kont: <b><?=$waiting_accounts?></b><br>
            <br>
            Ilość kont deweloperskich: <b><?=$developer_accounts?></b><br>
            Ilość kont administratorów: <b><?=$admin_accounts?></b><br>
            <br>
            Ilość zablokowanych kont: <b><?=$blocked_accounts?></b><br>
          </div>
          
        </div>
        <br><br>
        <h1>Wykorzystanie przestrzeni</h1>
        <h5>Informacja o ilości danych oraz plików przesłanych przez konkretnego użytkownika</h5>
        <hr>
        <br>
        <table cellspacing="0">
          <thead>
            <tr>
              <td style="width: 1px">LP</td>
              <td>Użytkownik</td>
              <td>Klasa</td>
              <td>Ilość plików</td>
              <td>Ilość folderów</td>
              <td>Zajęte miejsce</td>
              <td algh-right></td>
            </tr>
          </thead>
          <tbody>
            <?php
              $nus = usort($users_stats, function($a, $b) {
                return $b['bytes'] <=> $a['bytes'];
              });

              $lp = 1;

              foreach ($users_stats as $key => $value) {
                if($value['bytes'] == 0) continue;
                $userdata = sGetUserDataByLogin($value['name']);
                ?>
                  <tr>
                    <td><?=$lp?></td>
                    <td><?=$userdata['first_name']?> <?=$userdata['last_name']?></td>
                    <td><?=$userdata['class']?></td>
                    <td><?=number_format($value['files'], 0, ".", " ")?></td>
                    <td><?=number_format($value['folders'], 0, ".", " ")?></td>
                    <td><?=sFormatSizeUnits($value['bytes'])?></td>
                    <td align-right>
                      <a href="/<?=$value['name']?>">Pokaż stronę</a> | 
                      <a href="javascript:ShowMainDirectoryOfUser('<?=$value['name']?>')">Pokaż katalog główny</a>
                    </td>
                  </tr>
                <?php
                $lp++;
              }
            ?>
          </tbody>          
        </table>
      </div>
    </div>

    <div class="admin-tools-stats">
      <span class="title">Informacje o plikach</span>
      <div class="content">
        <div style="position: realtive; display: grid; grid-template-columns: auto 1fr; grid-gap: 25px; margin-bottom: 50px">
          <div style="position: relative; display: block; width: 600px;">
            <canvas id="overall_stats"></canvas>
            <script>
              <?php
                $technologies = array();
                $tech_counts = array();
                $tech_colors = array();

                $colorid = 0;

                foreach ($stats as $k => $v) {
                  if ($v['count'] == 0) continue;

                  $technologies[] = $v['name'];
                  $tech_counts[] = $v['count'];

                  $tech_colors[] = $colors2[$colorid];

                  $colorid++;
                }
              ?>

              var ctx = document.getElementById("overall_stats").getContext('2d');
              var overall_stats = new Chart(ctx, {
                type: 'pie',
                data: {
                  labels: <?=json_encode($technologies)?>,
                  datasets: [{
                    label: "Wykorzystane technologie",
                    data: <?=json_encode($tech_counts)?>,
                    backgroundColor: <?=json_encode($tech_colors)?>,
                    borderColor: <?=json_encode($tech_colors)?>
                  }],
                },
                options: {
                  responsive: true,
                  legend: {
                    display: true,
                    position: 'right'
                  },
                  beginAtZero: true,
                  stepSize: 100
                }
              });
            </script>
          </div>
          <div style="">
            <h1>Ogólne dane</h1>
            <div style="position: relative; display: block; padding: 15px; background: #00336670; box-shadow: inset 0px 0px 0px 1.5px #0af, 0px 0px 15px #0066aaa0; margin: 10px 0px; color: #fff; font: 16px YouTube Bold; border-radius: 5px">
              Pliki potrzebne do działania systemu zostały wykluczone ze statystyk.
            </div>
            Łączna ilość danych zapisanych na serwerze: <b><?=sFormatSizeUnits($bytes_count)?></b><br>
            Ilość plików na serwerze: <b><?=number_format($files_count, 0, ".", " ")?></b><br>
            Ilość folderów na serwerze: <b><?=number_format($dirs_count, 0, ".", " ")?></b><br>
          </div>
          
        </div>
        <br><br>
        <h1>Szczegółowy podział</h1>
        <h5>Poniżej przedstawiono szczegółowy podział technologii na rozszerzenia plików</h5>
        <hr>
        <div style="position: relative; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); grid-gap: 15px; margin-top: 50px">
          <?php

            $colorid = 0;

            foreach ($stats as $key => $value) {

              if ($value['count'] == 0) continue;

              $values = array();
              $counts = array();
              $colors = array();

              foreach ($value['detailed'] as $ext => $cnt) {
                $values[] = $ext;
                $counts[] = $cnt;
                $colors[] = $colors3[$colorid];

                $colorid++;
              }

              ?>
                <div style="position: relative; display: block; background: #ffffff0a; padding: 25px; align-content: center; border-radius: 10px; box-shadow: 0px 2px 5px #00000060">
                  <span style="font: 25px YouTube Bold; color: #fff; text-align: center; display: block; text-transform: uppercase"><?=$value['name']?></span>
                  <span style="font: 16px YouTube Light; color: #aaa; text-align: center; display: block; margin-bottom: 25px;">Ilość plików: <b><?=$value['count']?></b></span>
                  <div style="position: relative; display: block; padding: 15px; background: #ffffff10; border-radius: 5px; box-shadow: 0px 1px 3px #00000060">
                    <canvas id="<?=$key?>_chart"></canvas>
                  </div>              
                </div>

                <script>              
                  var ctx = document.getElementById("<?=$key?>_chart").getContext('2d');
                  var mychart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                      labels: <?=json_encode($values)?>,
                      datasets: [{
                        label: "Ilość plików",
                        data: <?=json_encode($counts)?>,
                        backgroundColor: <?=json_encode($colors)?>
                      }],
                    },
                    options: {
                      responsive: true,
                      legend: {
                        display: false
                      },
                      scales: {
                        yAxes: [{
                          ticks: {
                            beginAtZero: true,
                            stepSize: 100
                          }
                        }]
                      }
                      
                    }
                  });
                </script>
              <?php

              $colorid++;
            }
          ?>
        </div>
      </div>
    </div>    
  </div>
</div>