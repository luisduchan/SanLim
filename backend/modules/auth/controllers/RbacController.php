<?php

namespace backend\modules\auth\controllers;

use Yii;
use common\modules\auth\models\AuthItem;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RbacController implements the CRUD actions for AuthItem model.
 */
class RbacController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    //create permission
    public function actionCreate_permission()
    {
        $auth = Yii::$app->authManager;

//        // add "createPost" permission
//        $idnexBranch = $auth->createPermission('branch_index');
//        $idnexBranch->description = 'Index';
//        $auth->add($idnexBranch);
//
//        // add "updateBranch" permission
//        $viewBranch = $auth->createPermission('branch_view');
//        $viewBranch->description = 'View Branch';
//        $auth->add($viewBranch);
//        
//        // add "updateBranch" permission
//        $createBranch = $auth->createPermission('branch_create');
//        $createBranch->description = 'Create Branch';
//        $auth->add($createBranch);
//        
//        // add "updateBranch" permission
//        $updateBranch = $auth->createPermission('branch_update');
//        $updateBranch->description = 'Update Branch';
//        $auth->add($updateBranch);
//        
//        // add "updateBranch" permission
//        $deleteBranch = $auth->createPermission('branch_delete');
//        $deleteBranch->description = 'Delete Branch';
//        $auth->add($deleteBranch);
        
        
        // add "material out report" permission
        $materialOutputReport = $auth->createPermission('material_ouput_report');
        $materialOutputReport->description = 'Material Output Report';
        $auth->add($materialOutputReport);
    }
    
    public function actionCreate_role()
    {
        $auth = Yii::$app->authManager;
        $materialOutputReport = $auth->createPermission('material_ouput_report');
        
        $inventoryReport = $auth->createRole('inventory_report');
        $auth->add($inventoryReport);
        $auth->addChild($inventoryReport, $materialOutputReport);
        
    }
    
    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => AuthItem::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
