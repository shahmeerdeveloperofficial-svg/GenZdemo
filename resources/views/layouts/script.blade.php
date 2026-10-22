<!-- js file  -->
@if(session('payment_form_html'))
    <div id="virtualPaymentForm" style="display: none;">
        {!! session('payment_form_html') !!}
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Find the form inside the hidden div and submit it automatically
            var virtualForm = document.getElementById('virtualPaymentForm').querySelector('form');
            if (virtualForm) {
                virtualForm.submit();
            }
        });
    </script>
@endif

<div id="genz-toast-host" class="genz-toast-stack" role="region" aria-label="{{ __('Notifications') }}"></div>

<script src="{{asset('assets/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/dataTables.js')}}"></script>
<script src="{{asset('assets/js/plugins.js')}}"></script>
<script src="{{asset('assets/js/dataTables.responsive.min.js')}}"></script>
@if (request()->is('admin/cms/*', 'admin/cms-setting/*', 'admin/service/*', 'admin/subject*', 'admin/events*', 'admin/consultations*', 'admin/courses/programs*', 'admin/setting/email-edit*', 'consultant/consultant-profile'))
    <script src="{{asset('assets/css/summernote/summernote-lite.min.js')}}"></script>
@endif
@if (request()->is('admin', 'admin/dashboard'))
    <script src="{{asset('assets/js/apexcharts.min.js')}}"></script>
@endif
<script src="{{asset('assets/js/chart.js')}}"></script>
<script src="{{asset('assets/js/main.js')}}"></script>
<script src="{{ asset('common/js/common.js') }}?v=2"></script>

<script>
    window.GenzToastLabels = {
        success: @json(__('Saved')),
        info: @json(__('Information')),
        warning: @json(__('Please note')),
        error: @json(__('Something went wrong')),
        loading: @json(__('Working')),
        close: @json(__('Close notification')),
        closeButton: @json(__('Close')),
        open: @json(__('Open')),
        notifications: @json(__('Notifications'))
    };
</script>
<script src="{{ asset('genz/js/backend-toast.js') }}?v=4"></script>
<script src="{{ asset('genz/js/backend-shell.js') }}?v=5"></script>
<script src="{{ asset('genz/js/list-criteria.js') }}?v=1"></script>
<script src="{{ asset('genz/js/download-handler.js') }}?v=3"></script>
@if (request()->is('admin', 'admin/*', 'consultant', 'consultant/*', 'student', 'student/*', 'affiliate', 'affiliate/*', 'instructor', 'instructor/*', 'finance', 'finance/*', 'content', 'content/*'))
    <script src="{{ asset('genz/js/backend-table-tools.js') }}?v=6"></script>
@endif

@stack('script')

<script>
    var currencySymbol = "{{ getCurrencySymbol() }}";
    var currencyPlacement = "{{ getCurrencyPlacement() }}";

    @if (Session::has('success'))
        window.GenzToast ? window.GenzToast.success(@json(session('success'))) : toastr.success(@json(session('success')));
    @endif
    @if (Session::has('error'))
        window.GenzToast ? window.GenzToast.error(@json(session('error'))) : toastr.error(@json(session('error')));
    @endif
    @if (Session::has('info'))
        window.GenzToast ? window.GenzToast.info(@json(session('info'))) : toastr.info(@json(session('info')));
    @endif
    @if (Session::has('warning'))
        window.GenzToast ? window.GenzToast.warning(@json(session('warning'))) : toastr.warning(@json(session('warning')));
    @endif

    @if (@$errors->any())
        const validationErrors = @json($errors->all());
        const validationSummary = validationErrors.slice(0, 2).join('\n') + (validationErrors.length > 2 ? '\n+' + (validationErrors.length - 2) + ' more' : '');
        if (window.GenzToast) {
            window.GenzToast.error({
                title: @json(__('Please review the form')),
                message: validationSummary,
                action: {
                    label: @json(__('Review fields')),
                    onClick: function () {
                        const firstInvalid = document.querySelector('.is-invalid, [aria-invalid="true"]');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstInvalid.focus({ preventScroll: true });
                        }
                    }
                }
            });
        } else {
            validationErrors.forEach(function (message) { toastr.error(message); });
        }
    @endif
</script>
