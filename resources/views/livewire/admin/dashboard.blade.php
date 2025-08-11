<style>
    .dashboard-card {
        border-radius: 12px;
        background-color: #ffffff;
        color: #343a40;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #e0e0e0;
        border-left: 2px solid #007bff;
        /* Solid blue left border */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border 0.2s ease;
    }

    .dashboard-card:hover {
        transform: scale(1.03);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        border-color: #d0d0d0;
    }

    .dashboard-card .icon {
        font-size: 30px;
        color: #6c757d;
    }

    .dashboard-card .content h4 {
        margin: 0;
        font-size: 26px;
        font-weight: bold;
        color: #212529;
    }

    .dashboard-card .content small {
        font-size: 13px;
        color: #6c757d;
    }
</style>
<div class="row g-3">

    <!-- Asset Files -->
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="icon"><i class="bi bi-file-earmark-text"></i></div>
            <div class="content text-end">
                <small>Asset Files</small>
                <h4>{{ $assetFiles }}</h4>
            </div>
        </div>
    </div>

    <!-- Asset Details -->
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="icon"><i class="bi bi-box"></i></div>
            <div class="content text-end">
                <small>Assets</small>
                <h4>{{ $assetDetails }}</h4>
            </div>
        </div>
    </div>

    <!-- Drivers -->
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="icon"><i class="bi bi-person-fill-gear"></i></div>
            <div class="content text-end">
                <small>Drivers</small>
                <h4>{{ $drivers }}</h4>
            </div>
        </div>
    </div>

    <!-- Driver Assignments -->
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div class="content text-end">
                <small>Assignments</small>
                <h4>{{ $assignments }}</h4>
            </div>
        </div>
    </div>

    <!-- Business Units -->
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="icon"><i class="bi bi-building"></i></div>
            <div class="content text-end">
                <small>Business Units</small>
                <h4>{{ $businessUnits }}</h4>
            </div>
        </div>
    </div>
    <!-- Branches -->
    <div class="col-md-3">
        <div class="dashboard-card bg-gradient-teal">
            <div class="icon"><i class="bi bi-diagram-3"></i></div>
            <div class="content text-end">
                <small>Branches</small>
                <h4>{{ $branches }}</h4>
            </div>
        </div>
    </div>

    