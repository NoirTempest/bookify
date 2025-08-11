@switch($status)
@case('approved')
<span class="px-3 py-1 rounded-pill fw-bold text-success" style="background-color: #d1e7dd; border: 1px solid #badbcc;">
    Approved
</span>
@break

@case('pending')
<span class="px-3 py-1 rounded-pill fw-bold text-primary" style="background-color: #cfe2ff; border: 1px solid #9ec5fe;">
    Pending
</span>
@break

@case('rejected')
<span class="px-3 py-1 rounded-pill fw-bold text-danger" style="background-color: #f8d7da; border: 1px solid #f5c2c7;">
    Rejected
</span>
@break

@default
<span class="badge bg-secondary">Unknown</span>
@endswitch