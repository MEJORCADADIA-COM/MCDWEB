<div class="mt-5">
            <h4 class="text-center">Monthly Notes</h4>
            <div x-data="monthlyVictories">
                <ol class="appointment-list text-white" style="font-size: 1rem;">
                    <template x-for="data in list" :key="data.id">
                        <li class="border-bottom mb-1 me-3 pe-4 position-relative">
                            <span x-text="data.victory"></span>
                            <input class="check-items" type="checkbox" :checked="data.is_checked === 1" @change="(e) => updateIsChecked(e, data.id)">
                            <a class="edit-actions" @click="confirm('Are you sure you want to delete this item?') ? deleteMonthlyVictory(data.id) : ''">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                            </a>
                        </li>
                    </template>

                    <template x-for="i in textareaCount">
                        <li class="">
                            <div class="form-group">
                                <textarea class="w-100 text-white mt-2 bg-transparent text-bottom-border monthly-victory-textarea" /></textarea>
                            </div>
                        </li>
                    </template>

                </ol>

                <div class="form-group screenonly" style="padding:20px; text-align:right;">
                    <button class="button btn btn-info mb-1 mb-lg-0" @click="saveMonthlyVictories()" x-show="textareaCount > 0">
                        <i class="fa fa-save"></i> Guardar
                    </button>

                    <button class="button btn btn-info" @click="textareaCount++">
                        <i class="fa fa-book"></i> Agrega
                    </button>

                </div>

                <div :class="`alpine-alert alert ${alertClass}`" x-show="showAlert">
                    <p x-text="alertMsg"></p>
                </div>
            </div>
        </div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('monthlyVictories', () => ({
            list: <?= json_encode($monthlyVictories); ?>,
            showAlert: false,
            alertMsg: '',
            alertClass: '',
            textareaCount: 0,
            month: <?= $month; ?>,
            year: <?= $year; ?>,

            async updateIsChecked(e, victoryId) {
                const isChecked = e.target.checked;
                const request = await fetch(`<?= SITE_URL ?>/users/ajax/monthlyVictories.php`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        update_is_checked: true,
                        id: victoryId,
                        is_checked: isChecked
                    })
                });
                const response = await request.json();

                if (response.success) {
                    this.showToast('Note updated successfully');
                } else {
                    let message = response.message ?? 'Something went wrong. Try again later.'
                    this.showToast(message, 'error');
                    e.target.checked = false;
                }
            },

            async deleteMonthlyVictory(victoryId) {
                const request = await fetch(`<?= SITE_URL ?>/users/ajax/monthlyVictories.php`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        delete_victory: true,
                        id: victoryId,
                    })
                });
                const response = await request.json();

                if (response.success) {
                    this.showToast('Note deleted successfully');
                    this.list = this.list.filter(item => item.id !== victoryId);
                } else {
                    let message = response.message ?? 'Something went wrong. Try again later.'
                    this.showToast(message, 'error');
                }
            },

            async saveMonthlyVictories() {
                const victories = [];
                document.querySelectorAll('.monthly-victory-textarea').forEach(e => {
                    let value = e.value.trim();
                    if (value) {
                        victories.push(value);
                    }
                });

                if (victories.length === 0) return;

                const request = await fetch(`<?= SITE_URL ?>/users/ajax/monthlyVictories.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        add_monthly_victories: true,
                        victories: victories,
                        month: this.month,
                        year: this.year
                    })
                });

                const response = await request.json();

                if (response.success) {
                    this.showToast('Notes added successfully');
                    this.textareaCount = 0;
                    this.list = [...this.list, ...response.data.victories];
                } else {
                    let message = response.message ?? 'Something went wrong. Try again later.'
                    this.showToast(message, 'error');
                }
            },

            showToast(message, type = 'success') {
                this.alertMsg = message;
                this.showAlert = true;
                if (type === 'success') {
                    this.alertClass = 'alert-success';
                } else {
                    this.alertClass = 'alert-danger';
                }
                setTimeout(() => {
                    this.resetAlert();
                }, 3000)
            },

            resetAlert() {
                this.alertMsg = '';
                this.showAlert = false;
                this.alertClass = '';
            }
        }))
    })
</script>