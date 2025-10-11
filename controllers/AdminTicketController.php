<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;
use app\models\Ticket;
use app\models\TicketChat;
use app\models\Customer;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/**
 * AdminTicketController handles admin ticket management functionality
 */
class AdminTicketController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            // Only allow admin users (not customers)
                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            
                            // Check if user is a customer - if yes, deny access
                            $customer = Customer::find()->where(['user_id' => Yii::$app->user->id])->one();
                            if ($customer) {
                                return false; // Customers are not allowed
                            }
                            
                            // Allow non-customer users (admins)
                            return true;
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // Redirect customers to their dashboard
                    $customer = Customer::find()->where(['user_id' => Yii::$app->user->id])->one();
                    if ($customer) {
                        return Yii::$app->response->redirect(['/customer-dashboard/index']);
                    }
                    // Redirect guests to login
                    return Yii::$app->response->redirect(['/user/login']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'update-status' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all tickets for admin
     */
    public function actionIndex()
    {
        // Apply filters
        $statusFilter = Yii::$app->request->get('status', '');
        $priorityFilter = Yii::$app->request->get('priority', '');
        $customerFilter = Yii::$app->request->get('customer', '');

        // Build query for all tickets
        $query = Ticket::find()
            ->joinWith(['customer', 'customer.user'])
            ->orderBy(['created_at' => SORT_DESC]);

        // Apply status filter
        if (!empty($statusFilter)) {
            $query->andWhere(['ticket.status' => $statusFilter]);
        }

        // Apply priority filter
        if (!empty($priorityFilter)) {
            $query->andWhere(['ticket.priority' => $priorityFilter]);
        }

        // Apply customer filter
        if (!empty($customerFilter)) {
            $query->andWhere(['like', 'user.username', $customerFilter]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        // Get ticket statistics
        $stats = $this->getTicketStats();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'priorityFilter' => $priorityFilter,
            'customerFilter' => $customerFilter,
        ]);
    }

    /**
     * View a specific ticket
     */
    public function actionView($id)
    {
        $ticket = Ticket::find()
            ->joinWith(['customer', 'customer.user'])
            ->where(['ticket.id' => $id])
            ->one();

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found.');
        }

        // Get chat messages for this ticket
        $chatMessages = TicketChat::getTicketChats($ticket->id)->all();

        return $this->render('view', [
            'ticket' => $ticket,
            'chatMessages' => $chatMessages,
        ]);
    }

    /**
     * Send a chat message to a ticket (Admin)
     */
    public function actionSendMessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method.'];
        }

        $ticketId = Yii::$app->request->post('ticket_id');
        $message = Yii::$app->request->post('message');

        if (empty($ticketId) || empty($message)) {
            return ['success' => false, 'message' => 'Please provide ticket ID and message.'];
        }

        $ticket = Ticket::findOne($ticketId);

        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket not found.'];
        }

        // Add message to chat
        $result = TicketChat::addMessage($ticket->id, TicketChat::SENDER_ADMIN, Yii::$app->user->id, $message);

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Message sent successfully.',
                'chat' => [
                    'id' => $result['chat']->id,
                    'message' => $result['chat']->message,
                    'sender_name' => $result['chat']->getSenderName(),
                    'formatted_time' => $result['chat']->getFormattedTime(),
                    'sender_type' => $result['chat']->sender_type
                ]
            ];
        }

        return ['success' => false, 'message' => $result['message']];
    }

    /**
     * Update ticket status
     */
    public function actionUpdateStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Invalid request method.'];
        }

        $ticketId = Yii::$app->request->post('ticket_id');
        $status = Yii::$app->request->post('status');

        if (empty($ticketId) || empty($status)) {
            return ['success' => false, 'message' => 'Please provide ticket ID and status.'];
        }

        $ticket = Ticket::findOne($ticketId);

        if (!$ticket) {
            return ['success' => false, 'message' => 'Ticket not found.'];
        }

        $ticket->status = $status;
        $ticket->action_by = Yii::$app->user->id;
        $ticket->action_time = date('Y-m-d H:i:s');

        if ($ticket->save()) {
            return [
                'success' => true,
                'message' => 'Ticket status updated successfully.',
                'status_label' => $ticket->getStatusLabel()
            ];
        }

        return ['success' => false, 'message' => 'Failed to update ticket status: ' . implode(', ', $ticket->getFirstErrors())];
    }

    /**
     * Get ticket statistics
     */
    private function getTicketStats()
    {
        $total = Ticket::find()->count();
        $open = Ticket::find()->where(['status' => Ticket::STATUS_OPEN])->count();
        $inProgress = Ticket::find()->where(['status' => Ticket::STATUS_IN_PROGRESS])->count();
        $resolved = Ticket::find()->where(['status' => Ticket::STATUS_RESOLVED])->count();
        $closed = Ticket::find()->where(['status' => Ticket::STATUS_CLOSED])->count();

        return [
            'total' => $total,
            'open' => $open,
            'in_progress' => $inProgress,
            'resolved' => $resolved,
            'closed' => $closed,
        ];
    }
}
