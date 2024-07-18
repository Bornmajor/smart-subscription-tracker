<?php  $ratio = 40;?>
<div class="progress " role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height: 8px">
<div class="progress-bar <?php if($ratio >= 70){echo 'bg-danger'; }else{ echo 'bg-success';} ?>"   style="width: <?php echo $ratio.'%' ?>" ></div>
</div>


<div class="progress-circle 
<?php
if(expenseToBudgetRatio($budget_id) > 50){
  echo 'over50 ';  
}
  echo 'p'.expenseToBudgetRatio($budget_id)
?>
">
    <span><?php  echo expenseToBudgetRatio($budget_id)?>%</span>
    <div class="left-half-clipper">
        <div class="first50-bar"></div>
        <div class="value-bar"></div>
    </div>
</div>

  
<div class="progress-circle over50 p100">
    <span>30 %</span>
    <div class="left-half-clipper">
        <div class="first50-bar"></div>
        <div class="value-bar"></div>
    </div>
</div>
