<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\RewardPlan;

class RewardPlanController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => RewardPlan::find()->orderBy(['business_amount' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new RewardPlan();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Reward plan created successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Reward plan updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Reward plan deleted.');
        return $this->redirect(['index']);
    }

    public function actionManage()
    {
        $models = RewardPlan::find()->orderBy(['business_amount' => SORT_ASC])->all();
        if (empty($models)) {
            $models = [new RewardPlan()];
        }

        if (Yii::$app->request->isPost) {
            $models = [];
            $postData = Yii::$app->request->post();

            if (isset($postData['RewardPlan'])) {
                foreach ($postData['RewardPlan'] as $i => $item) {
                    if (isset($item['id']) && !empty($item['id'])) {
                        $models[$i] = RewardPlan::findOne($item['id']);
                    } else {
                        $models[$i] = new RewardPlan();
                    }
                    $models[$i]->attributes = $item;
                }
            }

            $valid = true;
            foreach ($models as $model) {
                $valid = $model->validate() && $valid;
            }

            if ($valid) {
                $tx = Yii::$app->db->beginTransaction();
                try {
                    foreach ($models as $model) {
                        if (!$model->save(false)) {
                            $tx->rollBack();
                            break;
                        }
                    }
                    $tx->commit();
                    Yii::$app->session->setFlash('success', 'Reward plans saved successfully.');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    $tx->rollBack();
                    Yii::$app->session->setFlash('error', 'Error saving reward plans: ' . $e->getMessage());
                }
            }
        }

        return $this->render('manage', ['models' => $models]);
    }

    protected function findModel($id)
    {
        if (($model = RewardPlan::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}


