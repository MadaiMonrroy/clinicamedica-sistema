<script>
    const staffModal = document.getElementById('staffModal');
    const staffModalBackdrop = document.getElementById('staffModalBackdrop');
    const staffForm = document.getElementById('staffForm');
    const staffFormMethod = document.getElementById('staffFormMethod');
    const staffModalTitle = document.getElementById('staffModalTitle');
    const toggleModal = document.getElementById('toggleModal');
    const toggleModalBackdrop = document.getElementById('toggleModalBackdrop');
    const toggleStatusForm = document.getElementById('toggleStatusForm');
    const toggleModalMessage = document.getElementById('toggleModalMessage');

    // DESPUÉS:
function openStaffCreateModal() {
    staffModal.classList.remove('hidden');
    staffModal.classList.add('flex');
    staffModalBackdrop.classList.remove('hidden');

    staffModalTitle.textContent = 'Nuevo personal';
    staffForm.action = "{{ route('admin.staff.store') }}";
    staffFormMethod.value = 'POST';
    staffForm.reset();

    document.getElementById('staff_activo').checked = true;
        document.getElementById('securitySection').classList.remove('hidden'); // ← mostrar

    document.getElementById('mailActivationNotice').classList.remove('hidden');
    document.getElementById('passwordEditSection').classList.add('hidden');

    // ← con setTimeout para esperar Alpine
    setTimeout(() => {
        window.staffFormBindings?.setRole('admin');
        window.staffFormBindings?.setEspecialidad('');
        window.staffFormBindings?.setCargo('');
    }, 30);
}
   function openStaffEditModal(user) {
    staffModal.classList.remove('hidden');
    staffModal.classList.add('flex');
    staffModalBackdrop.classList.remove('hidden');

    staffModalTitle.textContent = 'Editar personal';
    staffForm.action = `/admin/personal/${user.id}`;
    staffFormMethod.value = 'PATCH';

    document.getElementById('staff_name').value = user.name ?? '';
    document.getElementById('staff_apellido_paterno').value = user.apellido_paterno ?? '';
    document.getElementById('staff_apellido_materno').value = user.apellido_materno ?? '';
    document.getElementById('staff_ci').value = user.ci ?? '';
    document.getElementById('staff_telefono').value = user.telefono ?? '';
    document.getElementById('staff_email').value = user.email ?? '';
    document.getElementById('staff_activo').checked = !!user.activo;
    document.getElementById('securitySection').classList.add('hidden');    // ← ocultar todo

    document.getElementById('mailActivationNotice').classList.add('hidden');
    document.getElementById('passwordEditSection').classList.remove('hidden');
    document.getElementById('resendAccessForm').action = `/admin/personal/${user.id}/resend-access`;

    // ← con setTimeout para esperar Alpine
    setTimeout(() => {
        window.staffFormBindings?.setRole(user.rol ?? 'recepcionista');
        window.staffFormBindings?.setEspecialidad(user.especialidad ?? '');
        window.staffFormBindings?.setCargo(user.cargo ?? '');
    }, 30);
}
    function closeStaffModal() {
    staffModal.classList.add('hidden');
    staffModal.classList.remove('flex');
    staffModalBackdrop.classList.add('hidden');
    document.getElementById('securitySection')?.classList.remove('hidden'); // ← resetear

    document.getElementById('passwordEditSection')?.classList.add('hidden');    // ← resetear al cerrar
    document.getElementById('mailActivationNotice')?.classList.remove('hidden');// ← resetear al cerrar
}

    function openToggleStatusModal(userId, userName, isActive) {
        toggleModal.classList.remove('hidden');
        toggleModal.classList.add('flex');
        toggleModalBackdrop.classList.remove('hidden');

        toggleStatusForm.action = `/admin/personal/${userId}/toggle-active`;
        toggleModalMessage.textContent = isActive
            ? `¿Deseas inactivar a ${userName}?`
            : `¿Deseas activar a ${userName}?`;
    }

    function closeToggleStatusModal() {
        toggleModal.classList.add('hidden');
        toggleModal.classList.remove('flex');
        toggleModalBackdrop.classList.add('hidden');
    }

    staffModalBackdrop.addEventListener('click', closeStaffModal);
    toggleModalBackdrop.addEventListener('click', closeToggleStatusModal);
    function togglePasswordField(inputId, eyeOpenId, eyeClosedId) {
    const input = document.getElementById(inputId);
    const eyeOpen = document.getElementById(eyeOpenId);
    const eyeClosed = document.getElementById(eyeClosedId);

    if (!input || !eyeOpen || !eyeClosed) return;
 
    if (input.type === 'password') {
        input.type = 'text';
        eyeClosed.classList.add('hidden');
        eyeOpen.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeClosed.classList.remove('hidden');
        eyeOpen.classList.add('hidden');
    }
}
let staffFilterTimeout = null;

    async function runStaffFilters(url = null) {
        const search = document.getElementById('staff-search')?.value ?? '';
        const rol = document.getElementById('staff-role')?.value ?? '';

        const endpoint = url ?? `{{ route('admin.staff.index') }}`;
        const query = new URLSearchParams({
            search: search,
            rol: rol,
        });

        const finalUrl = `${endpoint}${endpoint.includes('?') ? '&' : '?'}${query.toString()}`;

        try {
            const response = await fetch(finalUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            document.getElementById('staff-table-wrapper').innerHTML = data.table;
            document.getElementById('staff-pagination-wrapper').innerHTML = data.pagination;

            window.history.replaceState({}, '', finalUrl);
            bindPaginationLinks();
        } catch (error) {
            console.error('Error al filtrar personal:', error);
        }
    }

    function bindPaginationLinks() {
        document.querySelectorAll('#staff-pagination-wrapper a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                runStaffFilters(this.href);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('staff-search');

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(staffFilterTimeout);
                staffFilterTimeout = setTimeout(() => {
                    runStaffFilters();
                }, 250);
            });
        }

        bindPaginationLinks();
    });
</script>