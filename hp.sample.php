<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <script src="js/jquery.js"></script>


<style>
body{
  width: 100%;
}
#logo{
  text-align:center;
  margin-top:100px;
  margin-bottom:100px;
}
img.size{
  width:220px;
  height:450px;
}
.item{
  margin-right:30px;
  margin-left:30px;
  text-align:left;
}
.no{
  display:inline-block;
  margin:20px;
}
a:hover img.size{
    cursor:pointer;
    filter: alpha(opacity=60);
    -ms-filter: "alpha(opacity=60)";
    -moz-opacity:0.6;
    -khtml-opacity: 0.6;
    opacity:0.3;
    zoom:1;
    transition: 0.7s ;
}
.kimi{
  height:40px;
  background-color:yellow;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size:14px;
  color:#094c9a;
  font-weight: bold;
}
a:link { color:#094c9a; text-decoration:underline
}
a:visited { color:#094c9a; text-decoration:underline
}
a:hover { color:#094c9a; text-decoration:underline
}
a:active { color:#094c9a; text-decoration:underline
}

.lock {
    overflow:hidden;
}
/*内枠*/
.modal-content {
    position:relative;
    display:none;
    width:80%;
    margin:0px;
    padding:20px 20px;
    border:2px solid #aaa;
    background:#fff;
}

.modal-content p {
    margin:0;
    padding:0;
    text-align:center;
}
/*外枠*/
.modal-overlay {
    z-index:1;
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:120%;
    background-color:/*透過*/rgba(0,0,0,0.75);
}

.modal-wrap {
    z-index:2;
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    overflow:auto;
}

.modal-open {
    color:#00f;
    text-decoration:underline;
}

.modal-open:hover {
    cursor:pointer;
    color:#f00;
}

.modal-close{
    cursor: pointer;
    position: fixed;
    top: 15px;
    right: 6px;
    padding: 0;
    display: block;
    color:gray;
    text-indent: -100px;
    font-size:50px;
}

.modal-close:hover {
    cursor:pointer;
    color:gray;
}

img.nakami{
  max-width:100%;
  width:auto;
  height:auto;
}
.sisaku{
  text-align:center;
}
.title{
  margin-top:100px;
  margin-bottom:40px;
}
img.subtitle{
  margin-top:40px;
}
.box-bottom{
  text-align:center;
  margin-top: 80px;

}
</style>

</head>
<body>
<header>
 <h1>
   <img src='img3/top.png' style="width:100%;">
 </h1>

</header>

<div id="logo"><img src='img3/merkmal_logo.png'></div>

<div class="item">
  <?php for($i=1;$i<=25;$i++){ ?>
    <div class="no"><a data-target="con<?php print $i; ?>" class="modal-open"><img src='img3/no<?php print $i ?>.png' class="size"></a></div>

  <?php } ?>
</div>

<div class="obi">
  <div class="kimi">8月26日より「  <a href="" target="_blank">君の名は。× FUN!TOKYO!モバイルスタンプラリー</a>  」キャンペーン開始します！</div>
</div>

<?php for($i=1;$i<=25;$i++){ ?>
<div id="con<?php print $i; ?>" class="modal-content">
  <p><img src='img3/merkmal_logo.png' class="subtitle"></p>
  <div class=title><img src='img3/<?php print $i;?>title.png'></div>
  <div class="sisaku"><img src='img3/<?php print $i;?>nakami.jpg' class="nakami"></a></div>
  <div class=box-bottom><img src='img3/copyright.png' class=nakami></div>
  <div class="modal-close">×</div>
</div>
<?php } ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="merkmal.js"></script>

</body>
</head>
