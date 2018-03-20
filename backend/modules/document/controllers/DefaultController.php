<?php

namespace backend\modules\document\controllers;
use backend\modules\common\models\DepartmentModel;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Default controller for the `document` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */

    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'login', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['view_document'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionTest()
    {
        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->getDepartmentTreeView();
//        var_dump($departments);
        return $this->render('test', [
            'departments' => $departments
        ]);
    }
}
