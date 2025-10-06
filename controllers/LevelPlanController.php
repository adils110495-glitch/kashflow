<?php

namespace app\controllers;

use app\controllers\base\LevelPlanController as BaseLevelPlanController;
use app\models\LevelPlan;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * LevelPlanController implements the CRUD actions for LevelPlan model.
 */
class LevelPlanController extends BaseLevelPlanController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Only authenticated users
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

    /**
     * Creates new LevelPlan models using dynamic form.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $models = [new LevelPlan()];
        
        if (Yii::$app->request->isPost) {
            $models = [];
            $postData = Yii::$app->request->post();
            
            if (isset($postData['LevelPlan'])) {
                foreach ($postData['LevelPlan'] as $i => $item) {
                    $models[$i] = new LevelPlan();
                    $models[$i]->attributes = $item;
                }
            }
            
            $valid = true;
            foreach ($models as $model) {
                $valid = $model->validate() && $valid;
            }
            
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($models as $model) {
                        if (!$model->save(false)) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Level plans created successfully.');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Error creating level plans: ' . $e->getMessage());
                }
            }
        }
        
        return $this->render('create', ['models' => $models]);
    }

    /**
     * Updates existing LevelPlan models using dynamic form.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $models = [LevelPlan::findOne($id)];
        
        if ($models[0] === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        if (Yii::$app->request->isPost) {
            $models = [];
            $postData = Yii::$app->request->post();
            
            if (isset($postData['LevelPlan'])) {
                foreach ($postData['LevelPlan'] as $i => $item) {
                    if (isset($item['id']) && !empty($item['id'])) {
                        $models[$i] = LevelPlan::findOne($item['id']);
                    } else {
                        $models[$i] = new LevelPlan();
                    }
                    $models[$i]->attributes = $item;
                }
            }
            
            $valid = true;
            foreach ($models as $model) {
                $valid = $model->validate() && $valid;
            }
            
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($models as $model) {
                        if (!$model->save(false)) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Level plans updated successfully.');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Error updating level plans: ' . $e->getMessage());
                }
            }
        }
        
        return $this->render('update', ['models' => $models]);
    }

    /**
     * Action to manage all level plans at once
     * @return string|Response
     */
    public function actionManage()
    {
        $models = LevelPlan::find()->orderBy('level ASC')->all();
        
        if (empty($models)) {
            $models = [new LevelPlan()];
        }
        
        if (Yii::$app->request->isPost) {
            $models = [];
            $postData = Yii::$app->request->post();
            
            if (isset($postData['LevelPlan'])) {
                foreach ($postData['LevelPlan'] as $i => $item) {
                    if (isset($item['id']) && !empty($item['id'])) {
                        $models[$i] = LevelPlan::findOne($item['id']);
                    } else {
                        $models[$i] = new LevelPlan();
                    }
                    $models[$i]->attributes = $item;
                }
            }
            
            $valid = true;
            foreach ($models as $model) {
                $valid = $model->validate() && $valid;
            }
            
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    foreach ($models as $model) {
                        if (!$model->save(false)) {
                            $transaction->rollBack();
                            break;
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Level plans saved successfully.');
                    return $this->redirect(['index']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Error saving level plans: ' . $e->getMessage());
                }
            }
        }
        
        return $this->render('manage', ['models' => $models]);
    }
}