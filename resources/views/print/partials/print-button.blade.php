<div class="page-print-actions">
    <button type="button" onclick="window.print()">Print</button>
</div>

<style>
    .page-print-actions {
        display: flex;
        justify-content: center;
        margin: 32px 0 24px;
    }

    .page-print-actions button {
        padding: 10px 32px;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        background: #2563eb;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .page-print-actions button:hover {
        background: #1d4ed8;
    }

    @media print {
        .page-print-actions {
            display: none !important;
        }
    }
</style>
