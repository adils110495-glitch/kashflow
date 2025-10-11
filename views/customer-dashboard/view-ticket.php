<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Ticket;

$this->title = 'Ticket #' . $ticket->id . ' - ' . Html::encode($ticket->subject);
$this->params['breadcrumbs'][] = ['label' => 'Support Tickets', 'url' => ['tickets']];
$this->params['breadcrumbs'][] = 'Ticket #' . $ticket->id;
?>

<div class="customer-dashboard-view-ticket">
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
                            <a href="<?= Url::to(['tickets']) ?>" class="btn btn-outline-secondary">
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
                                    <div class="chat-message mb-3 <?= $chat->sender_type === 'customer' ? 'customer-message' : 'admin-message' ?>">
                                        <div class="message-bubble p-3 rounded <?= $chat->sender_type === 'customer' ? 'bg-primary text-white ms-auto' : 'bg-white border' ?>" style="max-width: 70%;">
                                            <div class="message-content">
                                                <?= nl2br(Html::encode($chat->message)) ?>
                                            </div>
                                            <div class="message-meta mt-2">
                                                <small class="<?= $chat->sender_type === 'customer' ? 'text-white-50' : 'text-muted' ?>">
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
                        <?php if ($ticket->canCustomerCommunicate()): ?>
                            <div class="chat-input mt-3">
                                <form id="chat-form" class="d-flex gap-2">
                                    <input type="hidden" name="ticket_id" value="<?= $ticket->id ?>">
                                    <textarea name="message" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather icon-send"></i>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-3">
                                <i class="feather icon-lock me-2"></i>
                                <strong>Ticket Closed:</strong> This ticket is closed. You can view the conversation history but cannot send new messages.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Ticket Timeline -->
                    <div class="ticket-timeline">
                        <h6 class="text-muted mb-3">
                            <i class="feather icon-clock me-2"></i>
                            Timeline
                        </h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Ticket Created</h6>
                                    <small class="text-muted"><?= date('M d, Y \a\t h:i A', strtotime($ticket->created_at)) ?></small>
                                </div>
                            </div>
                            
                            <?php if ($ticket->status === Ticket::STATUS_IN_PROGRESS): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">In Progress</h6>
                                        <small class="text-muted">Your ticket is being reviewed by our support team</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($ticket->status === Ticket::STATUS_RESOLVED): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Resolved</h6>
                                        <small class="text-muted">Your issue has been resolved</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($ticket->status === Ticket::STATUS_CLOSED): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-secondary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Closed</h6>
                                        <small class="text-muted">This ticket has been closed</small>
                                    </div>
                                </div>
                            <?php endif; ?>
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

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-zap me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= Url::to(['create-ticket']) ?>" class="btn btn-outline-primary">
                            <i class="feather icon-plus me-1"></i>
                            Create New Ticket
                        </a>
                        <a href="<?= Url::to(['tickets']) ?>" class="btn btn-outline-secondary">
                            <i class="feather icon-list me-1"></i>
                            View All Tickets
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="feather icon-help-circle me-2"></i>
                        Need More Help?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        If you need immediate assistance or have additional questions, please create a new ticket.
                    </p>
                    <div class="d-grid">
                        <a href="<?= Url::to(['create-ticket']) ?>" class="btn btn-primary btn-sm">
                            <i class="feather icon-plus me-1"></i>
                            Create New Ticket
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

.response-content {
    line-height: 1.6;
    white-space: pre-wrap;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
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

.chat-message {
    display: flex;
    margin-bottom: 1rem;
}

.customer-message {
    justify-content: flex-end;
}

.admin-message {
    justify-content: flex-start;
}

.message-bubble {
    word-wrap: break-word;
}

.customer-message .message-bubble {
    background-color: #007bff;
    color: white;
}

.admin-message .message-bubble {
    background-color: white;
    border: 1px solid #dee2e6;
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
        messageDiv.className = 'chat-message mb-3 customer-message';
        
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
    
    // Auto-refresh chat messages every 30 seconds
    setInterval(function() {
        refreshChatMessages();
    }, 30000);
    
    function refreshChatMessages() {
        fetch('<?= Url::to(['get-chat-messages', 'id' => $ticket->id]) ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Only update if there are new messages
                const currentMessageCount = chatMessages.querySelectorAll('.chat-message').length;
                if (data.messages.length > currentMessageCount) {
                    // Reload the page to show new messages
                    location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing messages:', error);
        });
    }
    
    // Scroll to bottom on page load
    scrollToBottom();
});
</script>
