<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Ticket;

$this->title = 'Ticket #' . $ticket->id . ' - ' . Html::encode($ticket->subject);
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ticket #' . $ticket->id;
?>

<div class="admin-ticket-view">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="feather icon-file-text me-2"></i>
                                Ticket #<?= $ticket->id ?>
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                                <i class="feather icon-arrow-left me-1"></i>
                                Back to Tickets
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Ticket Header -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h4 class="text-primary"><?= Html::encode($ticket->subject) ?></h4>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <?= $ticket->getStatusLabel() ?>
                                <?= $ticket->getPriorityLabel() ?>
                                <small class="text-muted">
                                    <i class="feather icon-calendar me-1"></i>
                                    Created <?= date('M d, Y \a\t h:i A', strtotime($ticket->created_at)) ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="ticket-id-display">
                                <span class="badge bg-light text-dark fs-6">#<?= $ticket->id ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="customer-info mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="feather icon-user me-2"></i>
                            Customer Information
                        </h6>
                        <div class="customer-details p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong> <?= Html::encode($ticket->customer->name) ?><br>
                                    <strong>Username:</strong> @<?= Html::encode($ticket->customer->user->username) ?><br>
                                    <strong>Email:</strong> <?= Html::encode($ticket->customer->user->email) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Package:</strong> <?= $ticket->customer->currentPackage ? Html::encode($ticket->customer->currentPackage->name) : 'No Package' ?><br>
                                    <strong>Country:</strong> <?= $ticket->customer->country ? Html::encode($ticket->customer->country->name) : 'N/A' ?><br>
                                    <strong>Joined:</strong> <?= date('M d, Y', strtotime($ticket->customer->created_at)) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Description -->
                    <div class="ticket-description mb-4">
                        <h6 class="text-muted mb-3">
                            <i class="feather icon-message-square me-2"></i>
                            Description
                        </h6>
                        <div class="description-content p-3 bg-light rounded">
                            <?= nl2br(Html::encode($ticket->description)) ?>
                        </div>
                    </div>

                    <!-- Chat Interface -->
                    <div class="chat-interface mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="feather icon-message-circle me-2"></i>
                            Conversation
                        </h6>
                        
                        <!-- Chat Messages -->
                        <div class="chat-messages p-3 bg-light rounded" style="height: 400px; overflow-y: auto;" id="chat-messages">
                            <?php if (empty($chatMessages)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="feather icon-message-circle fa-2x mb-2"></i>
                                    <p>No messages yet. Start the conversation below.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($chatMessages as $chat): ?>
                                    <div class="chat-message mb-3 <?= $chat->sender_type === 'admin' ? 'admin-message' : 'customer-message' ?>">
                                        <div class="message-bubble p-3 rounded <?= $chat->sender_type === 'admin' ? 'bg-primary text-white ms-auto' : 'bg-white border' ?>" style="max-width: 70%;">
                                            <div class="message-content">
                                                <?= nl2br(Html::encode($chat->message)) ?>
                                            </div>
                                            <div class="message-meta mt-2">
                                                <small class="<?= $chat->sender_type === 'admin' ? 'text-white-50' : 'text-muted' ?>">
                                                    <strong><?= Html::encode($chat->getSenderName()) ?></strong> • 
                                                    <?= $chat->getFormattedTime() ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Chat Input -->
                        <div class="chat-input mt-3">
                            <form id="chat-form" class="d-flex gap-2">
                                <input type="hidden" name="ticket_id" value="<?= $ticket->id ?>">
                                <textarea name="message" class="form-control" rows="2" placeholder="Type your response..." required></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-send"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ticket Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-info me-2"></i>
                        Ticket Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <strong>Status:</strong><br>
                        <?= $ticket->getStatusLabel() ?>
                    </div>
                    <div class="info-item mb-3">
                        <strong>Priority:</strong><br>
                        <?= $ticket->getPriorityLabel() ?>
                    </div>
                    <div class="info-item mb-3">
                        <strong>Created:</strong><br>
                        <small class="text-muted"><?= date('M d, Y \a\t h:i A', strtotime($ticket->created_at)) ?></small>
                    </div>
                    <div class="info-item mb-3">
                        <strong>Last Updated:</strong><br>
                        <small class="text-muted"><?= date('M d, Y \a\t h:i A', strtotime($ticket->updated_at)) ?></small>
                    </div>
                </div>
            </div>

            <!-- Status Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-settings me-2"></i>
                        Status Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-warning" onclick="updateStatus(<?= $ticket->id ?>, <?= Ticket::STATUS_OPEN ?>)">
                            <i class="feather icon-clock me-1"></i>
                            Mark as Open
                        </button>
                        <button class="btn btn-outline-info" onclick="updateStatus(<?= $ticket->id ?>, <?= Ticket::STATUS_IN_PROGRESS ?>)">
                            <i class="feather icon-loader me-1"></i>
                            Mark as In Progress
                        </button>
                        <button class="btn btn-outline-success" onclick="updateStatus(<?= $ticket->id ?>, <?= Ticket::STATUS_RESOLVED ?>)">
                            <i class="feather icon-check-circle me-1"></i>
                            Mark as Resolved
                        </button>
                        <button class="btn btn-outline-secondary" onclick="updateStatus(<?= $ticket->id ?>, <?= Ticket::STATUS_CLOSED ?>)">
                            <i class="feather icon-x-circle me-1"></i>
                            Mark as Closed
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-zap me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-primary">
                            <i class="feather icon-list me-1"></i>
                            View All Tickets
                        </a>
                        <a href="<?= Url::to(['index', 'status' => '1']) ?>" class="btn btn-outline-warning">
                            <i class="feather icon-clock me-1"></i>
                            Pending Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.description-content {
    line-height: 1.6;
    white-space: pre-wrap;
}

.customer-details {
    line-height: 1.6;
}

.chat-message {
    display: flex;
    margin-bottom: 1rem;
}

.customer-message {
    justify-content: flex-start;
}

.admin-message {
    justify-content: flex-end;
}

.message-bubble {
    word-wrap: break-word;
}

.customer-message .message-bubble {
    background-color: white;
    border: 1px solid #dee2e6;
}

.admin-message .message-bubble {
    background-color: #007bff;
    color: white;
}

#chat-messages {
    scrollbar-width: thin;
}

#chat-messages::-webkit-scrollbar {
    width: 6px;
}

#chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.info-item {
    padding-bottom: 10px;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.ticket-id-display {
    font-size: 1.1rem;
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.text-primary {
    color: #007bff !important;
}

.text-success {
    color: #28a745 !important;
}

.text-muted {
    color: #6c757d !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.querySelector('textarea[name="message"]');
    
    if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(chatForm);
            const message = formData.get('message').trim();
            
            if (!message) {
                alert('Please enter a message.');
                return;
            }
            
            // Disable form during submission
            const submitBtn = chatForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="feather icon-loader fa-spin"></i>';
            submitBtn.disabled = true;
            
            // Send message via AJAX
            fetch('<?= Url::to(['send-message']) ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add message to chat
                    addMessageToChat(data.chat);
                    messageInput.value = '';
                    scrollToBottom();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the message.');
            })
            .finally(() => {
                // Re-enable form
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    function addMessageToChat(chat) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message mb-3 admin-message';
        
        messageDiv.innerHTML = `
            <div class="message-bubble p-3 rounded bg-primary text-white ms-auto" style="max-width: 70%;">
                <div class="message-content">
                    ${escapeHtml(chat.message)}
                </div>
                <div class="message-meta mt-2">
                    <small class="text-white-50">
                        <strong>${escapeHtml(chat.sender_name)}</strong> • 
                        ${chat.formatted_time}
                    </small>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
    }
    
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Scroll to bottom on page load
    scrollToBottom();
});

function updateStatus(ticketId, status) {
    if (confirm('Are you sure you want to update the ticket status?')) {
        fetch('<?= Url::to(['update-status']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            },
            body: 'ticket_id=' + ticketId + '&status=' + status
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Ticket status updated successfully.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    }
}
</script>
