<?php
/**
 * @author Harry Tang <harry@powerkernel.com>
 * @link https://powerkernel.com
 * @copyright Copyright (c) 2017 Power Kernel
 */

namespace powerkernel\support\controllers;

use common\components\BackendFilter;
use common\components\MainController;
use powerkernel\support\models\Content;
use Yii;
use powerkernel\support\models\Ticket;
use powerkernel\support\models\TicketSearch;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends MainController
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

            'backend' => [
                'class' => BackendFilter::className(),
                'actions' => [
                    'index',
                ],
            ],

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['manage', 'create', 'view', 'close'],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {

        $this->view->title = Yii::t('support', 'Tickets');
        $searchModel = new TicketSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all user Ticket models.
     * @return mixed
     */
    public function actionManage()
    {

        $this->layout = Yii::$app->view->theme->basePath . '/account.php';
        $this->view->title = Yii::t('support', 'My Tickets');
        $searchModel = new TicketSearch(['userSearch'=>true]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('manage', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        if(Yii::$app->id == 'app-frontend'){
            $this->layout = Yii::$app->view->theme->basePath . '/account.php';
        }
        if(Yii::$app->id == 'app-backend'){
            $this->layout = Yii::$app->view->theme->basePath . '/admin.php';
        }

        $model=$this->findModel($id);
        if (!Yii::$app->user->can('viewOwnItem', ['model' => $model]) && !Yii::$app->user->can('admin')) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        /* metaData */
        //$title=$model->title;
        $this->view->title = $model->title;
        //$keywords = $model->tags;
        //$description = $model->desc;
        //$metaTags[]=['name'=>'keywords', 'content'=>$keywords];
        //$metaTags[]=['name'=>'description', 'content'=>$description];
        /* Facebook */
        //$metaTags[]=['property' => 'og:title', 'content' => $title];
        //$metaTags[]=['property' => 'og:description', 'content' => $description];
        //$metaTags[]=['property' => 'og:type', 'content' => '']; // article, product, profile etc
        //$metaTags[]=['property' => 'og:image', 'content' => '']; //best 1200 x 630
        //$metaTags[]=['property' => 'og:url', 'content' => ''];
        //$metaTags[]=['property' => 'fb:app_id', 'content' => ''];
        //$metaTags[]=['property' => 'fb:admins', 'content' => ''];
        /* Twitter */
        //$metaTags[]=['name'=>'twitter:card', 'content'=>'summary_large_image']; // summary, summary_large_image, photo, gallery, product, app, player
        //$metaTags[]=['name'=>'twitter:site', 'content'=>Setting::getValue('twitterSite')];
        // Can skip b/c we already have og
        //$metaTags[]=['name'=>'twitter:title', 'content'=>$title];
        //$metaTags[]=['name'=>'twitter:description', 'content'=>$description];
        //$metaTags[]=['name'=>'twitter:image', 'content'=>''];
        //$metaTags[]=['name'=>'twitter:data1', 'content'=>''];
        //$metaTags[]=['name'=>'twitter:label1', 'content'=>''];
        //$metaTags[]=['name'=>'twitter:data2', 'content'=>''];
        //$metaTags[]=['name'=>'twitter:label2', 'content'=>''];
        /* jsonld */
        //$imageObject=$model->getImageObject();
        //$jsonLd = (object)[
        //    '@type'=>'Article',
        //    'http://schema.org/name' => $model->title,
        //    'http://schema.org/headline'=>$model->desc,
        //    'http://schema.org/articleBody'=>$model->content,
        //    'http://schema.org/dateCreated' => Yii::$app->formatter->asDate($model->created_at, 'php:c'),
        //    'http://schema.org/dateModified' => Yii::$app->formatter->asDate($model->updated_at, 'php:c'),
        //    'http://schema.org/datePublished' => Yii::$app->formatter->asDate($model->published_at, 'php:c'),
        //    'http://schema.org/url'=>Yii::$app->urlManager->createAbsoluteUrl($model->viewUrl),
        //    'http://schema.org/image'=>(object)[
        //        '@type'=>'ImageObject',
        //        'http://schema.org/url'=>$imageObject['url'],
        //        'http://schema.org/width'=>$imageObject['width'],
        //        'http://schema.org/height'=>$imageObject['height']
        //    ],
        //    'http://schema.org/author'=>(object)[
        //        '@type'=>'Person',
        //        'http://schema.org/name' => $model->author->fullname,
        //    ],
        //    'http://schema.org/publisher'=>(object)[
        //    '@type'=>'Organization',
        //    'http://schema.org/name'=>Yii::$app->name,
        //   'http://schema.org/logo'=>(object)[
        //        '@type'=>'ImageObject',
        //       'http://schema.org/url'=>Yii::$app->urlManager->createAbsoluteUrl(Yii::$app->homeUrl.'/images/logo.png')
        //    ]
        //    ],
        //    'http://schema.org/mainEntityOfPage'=>(object)[
        //        '@type'=>'WebPage',
        //        '@id'=>Yii::$app->urlManager->createAbsoluteUrl($model->viewUrl)
        //    ]
        //];

        /* OK */
        //$data['title']=$title;
        //$data['metaTags']=$metaTags;
        //$data['jsonLd']=$jsonLd;
        //$this->registerMetaTagJsonLD($data);

        // reply
        $reply=new Content();
        $reply->id_ticket=$model->id;
        $reply->created_by=Yii::$app->user->id;
        if ($reply->load(Yii::$app->request->post()) && $reply->save()) {
            if(Yii::$app->user->id==$model->created_by){
                $model->status=Ticket::STATUS_OPEN;
                $model->save();
            }
            else {
                $model->status=Ticket::STATUS_WAITING;
                $model->save();
            }
            return $this->redirect(['view', 'id' => is_a($model, '\yii\mongodb\ActiveRecord')?(string)$model->_id:$model->id]);
        }



        return $this->render('view', [
            'model' => $model,
            'reply'=>$reply
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = Yii::$app->view->theme->basePath . '/account.php';
        $this->view->title = Yii::t('support', 'Open Ticket');
        $model = new Ticket();
        $model->setScenario('create');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => is_a($model, '\yii\mongodb\ActiveRecord')?(string)$model->_id:$model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * close a ticket
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionClose($id){
        $model = $this->findModel($id);
        if (!Yii::$app->user->can('viewOwnItem', ['model' => $model]) && !Yii::$app->user->can('admin')) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        if($model->status!=Ticket::STATUS_CLOSED){
            $post=new Content();
            $post->id_ticket=$model->id;
            $post->created_by=Yii::$app->user->id;
            $post->content=Yii::$app->getModule('support')->t('{USER} closed the ticket.', ['USER'=>Yii::$app->user->identity->fullname]);
            if($post->save()){
                $model->status=Ticket::STATUS_CLOSED;
                if(!$model->save()){
                    Yii::$app->session->setFlash('danger', json_encode($model->errors));
                }
            }
        }

        return $this->redirect(['view', 'id' => is_a($model, '\yii\mongodb\ActiveRecord')?(string)$model->_id:$model->id]);
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
