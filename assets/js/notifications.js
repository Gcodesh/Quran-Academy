class NotificationSystem {
    constructor() {
        this.checkInterval = 30000; // 30 seconds
        this.init();
    }

    init() {
        this.checkNotifications();
        setInterval(() => this.checkNotifications(), this.checkInterval);

        const bell = document.getElementById('notification-bell');
        if (bell) {
            bell.addEventListener('click', () => {
                // Logic to show dropdown or redirect
                console.log('Show notifications');
            });
        }
    }

    async checkNotifications() {
        try {
            const response = await fetch('/islamic-education-platform/api/notifications.php'); // path adjusted
            if (response.ok) {
                const data = await response.json();

                if (data.count > 0) {
                    this.updateBadge(data.count);

                    if (data.important && "Notification" in window && Notification.permission === "granted") {
                        new Notification(data.latest.title, { body: data.latest.message });
                    }
                } else {
                    this.updateBadge(0);
                }
            }
        } catch (e) {
            console.error('Notification check failed', e);
        }
    }

    updateBadge(count) {
        const badge = document.getElementById('notification-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
}

// Request permission
if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
    Notification.requestPermission();
}

document.addEventListener('DOMContentLoaded', () => {
    new NotificationSystem();
});
