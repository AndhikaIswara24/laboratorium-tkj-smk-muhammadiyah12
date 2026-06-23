// Basic frontend script: sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function () {
	const sidebar = document.getElementById('sidebar');
	const backdrop = document.getElementById('sidebarBackdrop');
	const toggle = document.getElementById('sidebarToggle');

	if (!sidebar || !toggle) return;

	function openSidebar() {
		sidebar.classList.remove('-translate-x-full');
		backdrop.classList.remove('hidden');
	}

	function closeSidebar() {
		sidebar.classList.add('-translate-x-full');
		backdrop.classList.add('hidden');
	}

	toggle.addEventListener('click', function () {
		if (sidebar.classList.contains('-translate-x-full')) {
			openSidebar();
		} else {
			closeSidebar();
		}
	});

	if (backdrop) {
		backdrop.addEventListener('click', closeSidebar);
	}
});
