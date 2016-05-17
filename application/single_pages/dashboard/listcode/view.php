<?php
  defined('C5_EXECUTE') or die("Access Denied.");
  $h = Loader::helper('concrete/dashboard');
  $db = Loader::db();
  $valt = Loader::helper('validation/token');
  $qsid = (int)$_REQUEST['qsid'];
?>
<?php if($mode == 'list'){ ?>
<table class="table table-striped">
<thead>
</thead>
<thbody>
<?php foreach($anserlist as $album) { ?>
<tr>
  <td><?=$album['bID'] ?></td><td><?=$album['formname'] ?></td><td><?=$album['created'] ?></td><td><a href="<?php echo $view->action('datadetail'). '?qsid=' .$album['bID']; ?>" class="btn btn-default">Data</a></td>
</tr>
<? } ?>
<thbody>
</table>
<?php } ?>
  
<?php if($mode == 'detail'){  ?>
<div class="ccm-dashboard-header-buttons">
  <a id="ccm-export-results" class="btn btn-default" href="<?php echo $view->action('')?>">
      <i class='fa fa-angle-left'></i> <?php echo t('Back') ?>
  </a>
  <a id="ccm-export-results" class="btn btn-success" href="<?php echo $view->action('csv')?>?csvid=<?php echo $qsid; ?>">
      <i class='fa fa-download'></i> <?php echo t('Export to CSV') ?>
  </a>
</div>
<table class="table table-striped">
  <thead>
  <tr>
  <?php
  $asID = $datalist[0]['asID'];
  $q = $db->createQueryBuilder();
  $q->select('*')->from('ListcodeFormAnswerTable')->where('asID = :asID')->setParameter('asID', $asID);
  $r = $q->Execute();
  while ($row = $r->fetchRow()) {
  ?>
  <td><?=$row['label']?></td>
  <? } ?>
  <td><?=t('created')?></td>
  </tr>
  </thead>
   <thbody>
  <?php foreach($datalist as $dlist) { ?>
  <?php
  $asID = $dlist['asID'];
  $q = $db->createQueryBuilder();
  $q->select('*')->from('ListcodeFormAnswerTable')->where('asID = :asID')->setParameter('asID', $asID);
  $r = $q->Execute();
  ?>
  <tr>
    <?php
    while ($row = $r->fetchRow()) {
    ?>
    <td><?php
      if($row['answer'] != NULL){
        echo $row['answer'];
      }else{
        echo $row['answerLong'];
      }
      
    ?></td>
    <?php } ?>
  <td><?=$dlist['created'] ?></td>
  </tr>
  <? } ?>
  <thbody>
</table>
<?php }  ?>
<?php if($mode == 'csv'){  ?>
<?php }  ?>