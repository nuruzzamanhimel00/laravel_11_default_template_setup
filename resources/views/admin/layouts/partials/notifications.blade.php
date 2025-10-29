<div  class="dropdown d-inline-block" x-data="notificationsData({{ auth()->user()->id }})">
    <button type="button" class="btn header-item noti-icon"
            id="page-header-notifications-dropdown"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="mdi mdi-bell-outline"></i>
        <span class="badge bg-danger py-1 px-2 rounded-pill" x-text="total_unread"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown"
        >
        <div class="p-3">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="m-0 font-size-16" x-text="`Notifications (${total})`">  </h5>
                </div>
            </div>
        </div>
        <div data-simplebar style="max-height: 230px;">

            <template x-for="notify in all_notifications" :key="notify.id">
                <a href="#" @click="markAsRead(notify)" class="text-reset notification-item">
                    <div :class="`d-flex  ${notify.read_at != null ? '' : 'unread' }`">
                        <div class="flex-grow-1">
                            <div style="overflow: hidden">
                                <div class="text-start" style="float:left;"> <h6 class="mb-1" x-text="`Order ${notify.data.status}`"></h6></div>
                                <div class="text-end" style="float: right" x-text="notify.created_at_human"> </div>
                            </div>
                            <div class="font-size-12 text-muted">
                                <p class="mb-1" x-text="`${notify.data.message}`"></p>
                            </div>
                        </div>
                    </div>
                </a>
            </template>
            <template x-if="loading">
                <div class="text-center">Loading...</div>
            </template>


            <audio id="notification-sound" src="/sounds/notification.mp3" preload="auto"></audio>

        </div>
        <a href="#" @click="markAllAsRead" id="notification-all-item" class="text-center notify-all text-muted d-block w-100 notification-all-item py-3">
            View all <i class="fi-arrow-right"></i>
        </a>

    </div>
</div>
{{-- @push('style')
<style>
    .unread{
        background: #dddddd6e !important;
    }
</style>

@endpush --}}
@push('script')
<style>
    .unread{
        background: #dddddd6e !important;
    }
</style>
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notificationsData', (auth_id) => ({
                auth_id,
                all_notifications: [],
                loading: false,
                hasMore: true,
                page: 1,
                total:0,
                total_unread:0,
                async markAllAsRead() {
                    let self = this
                    await $.ajax({
                        url: `/notification-read-all`,
                        method: 'get',
                        dataType: 'json',
                        success: async (response) => {
                            // window.location.href = notify.data.visit_url
                            console.log('response', response)
                            if(response){
                                self.all_notifications =self.all_notifications.map((notify) => {
                                    return {
                                        ...notify,
                                        read_at: new Date()
                                    }
                                })
                                self.total_unread = 0
                            }
                        },
                        error: (error) => {
                            console.error('Error deleting location:', error);
                        }
                    });
                    // console.log('notify',notify)
                },
                async markAsRead(notify) {
                    await $.ajax({
                        url: `/notification-read/${notify.id}`,
                        method: 'get',
                        dataType: 'json',
                        success: async (response) => {
                            window.location.href = notify.data.visit_url
                            // console.log('response', response)
                        },
                        error: (error) => {
                            console.error('Error deleting location:', error);
                        }
                    });
                    // console.log('notify',notify)
                },
                async loadMore() {
                    if (this.loading || !this.hasMore) return;

                    this.loading = true;

                    try {
                        const res = await fetch(`/notifications?page=${this.page}`);
                        const response = await res.json();
                        this.total = response.total;
                        this.total_unread += response.data.reduce((total, notify) => total + (notify.read_at == null ? 1 : 0), 0);
                        // console.log('json', response);

                        if (response.data.length === 0) {
                            this.hasMore = false;
                        } else {
                            // this.all_notifications = this.all_notifications.concat(response.data);
                            this.all_notifications.push(...response.data)
                            this.page++;

                        }

                    } catch (error) {
                        console.error("Error loading notifications:", error);
                    } finally {
                        this.loading = false;
                    }
                },
                soundPlay(){
                    // Play sound
                    document.getElementById('notification-sound').play();
                },

                init() {
                    this.loadMore();

                    this.$nextTick(() => {
                        const simplebarContent = document.querySelector('[data-simplebar] .simplebar-content-wrapper');

                        if (simplebarContent) {
                            simplebarContent.addEventListener('scroll', (event) => {
                                const el = event.target;
                                if (el.scrollTop + el.clientHeight >= el.scrollHeight - 50) {
                                    this.loadMore();
                                }
                            });
                        }


                        Echo.private(`order-notify.${this.auth_id}`)
                        .listen("OrderNotifyEvent", (response) => {
                            if (response?.notify) {
                                // Add the new notification to the beginning of the array
                                this.all_notifications.unshift(response.notify);

                                // Optional: update total counters
                                this.total_unread += response.notify.read_at == null ? 1 : 0;
                                // Play sound
                                this.soundPlay()
                            }
                        });

                    });

                }
            }));
        });
    </script>
@endpush
