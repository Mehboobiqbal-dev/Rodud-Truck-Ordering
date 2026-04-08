@extends('admin.layouts.app')

@section('title', 'Messages')
@section('header-title', 'Messages')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-white font-bold inline-flex items-center">
                <i class="fas fa-envelope mr-3 text-indigo-400"></i>
                Messages
            </h1>
            <p class="text-sm text-slate-400 mt-1">View and respond to customer support messages</p>
        </div>
    </div>

    <!-- Messages List -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-lg shadow-black/20 overflow-hidden">
        @if($messages->count() > 0)
            <div class="divide-y divide-slate-800">
                @foreach($messages as $message)
                    <div class="px-6 py-5 transition-colors hover:bg-slate-800/50 {{ !$message->read_at ? 'bg-indigo-500/5' : '' }}">
                        <div class="flex items-start gap-4">
                            <!-- User Avatar -->
                            <div class="w-12 h-12 rounded-full shrink-0 flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-semibold">
                                {{ substr($message->user->name, 0, 1) }}
                            </div>

                            <!-- Message Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h3 class="text-sm font-semibold text-white">
                                            {{ $message->user->name }}
                                            <span class="text-xs text-slate-400 ml-2">{{ $message->user->email }}</span>
                                        </h3>
                                        <p class="text-xs text-slate-500">
                                            <i class="far fa-clock mr-1"></i> {{ $message->created_at->diffForHumans() }}
                                            @if($message->order)
                                                • Order #{{ $message->order->id }}
                                            @endif
                                        </p>
                                    </div>
                                    @if(!$message->read_at)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-400">
                                            New
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-sm font-medium text-slate-300 mb-2">{{ $message->subject }}</h4>
                                <p class="text-sm text-slate-400 line-clamp-3">{{ $message->message }}</p>

                                <!-- Actions -->
                                <div class="flex items-center gap-3 mt-3">
                                    <button onclick="openReplyModal({{ $message->id }}, '{{ $message->user->name }}', '{{ $message->subject }}')"
                                            class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                    @if($message->order)
                                        <a href="{{ route('admin.orders.show', $message->order->id) }}"
                                           class="text-xs font-medium text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                                            <i class="fas fa-eye"></i> View Order
                                        </a>
                                    @endif
                                    @if(!$message->read_at)
                                        <form action="{{ route('admin.messages.markRead', $message->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                                                <i class="fas fa-check"></i> Mark Read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-slate-800">
                {{ $messages->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i class="far fa-envelope text-4xl text-slate-600 mb-4"></i>
                <h3 class="text-lg font-medium text-white mb-2">No messages yet</h3>
                <p class="text-slate-400">Customer support messages will appear here.</p>
            </div>
        @endif
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="px-6 py-4 border-b border-slate-800">
                <h3 class="text-lg font-semibold text-white">Reply to Message</h3>
                <p id="replyToInfo" class="text-sm text-slate-400 mt-1"></p>
            </div>

            <form id="replyForm" action="{{ route('admin.messages.reply') }}" method="POST">
                @csrf
                <input type="hidden" name="message_id" id="messageIdInput">

                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Subject</label>
                        <input type="text" name="subject" id="replySubject" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Message</label>
                        <textarea name="message" rows="4" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none" placeholder="Type your reply..." required></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-800 flex justify-end gap-3">
                    <button type="button" onclick="closeReplyModal()" class="px-4 py-2 text-sm font-medium text-slate-400 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReplyModal(messageId, userName, originalSubject) {
    document.getElementById('messageIdInput').value = messageId;
    document.getElementById('replyToInfo').textContent = `Replying to ${userName}`;
    document.getElementById('replySubject').value = `Re: ${originalSubject}`;
    document.getElementById('replyModal').classList.remove('hidden');
    document.getElementById('replySubject').focus();
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
    document.getElementById('replyForm').reset();
}

// Close modal when clicking outside
document.getElementById('replyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReplyModal();
    }
});
</script>
@endsection