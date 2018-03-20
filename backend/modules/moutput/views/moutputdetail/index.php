<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\moutput\models\MoutputdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Material Out Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-out-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Material Out Detail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'division_code',
            'workcenter_code',
            'machine_code',
            'item_code',
            'document_no',
            'used_mass',
            'uom',
            'create_date',
            'last_update',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
