<style>
    .player-doc-wrapper {
        background: #f5f7fb;
        padding: 32px 16px;
    }

    .player-doc-card {
        max-width: 900px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        font-family: "Inter", "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        color: #0f172a;
        overflow: hidden;
    }

    .player-doc-header {
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        padding: 32px;
        color: #f8fafc;
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: flex-start;
    }

    .player-doc-eyebrow {
        text-transform: uppercase;
        letter-spacing: 0.32em;
        font-size: 12px;
        font-weight: 600;
        color: rgba(248, 250, 252, 0.8);
        margin-bottom: 8px;
    }

    .player-doc-title {
        font-size: 34px;
        margin: 0;
        font-weight: 700;
    }

    .player-doc-subtitle {
        margin-top: 8px;
        font-size: 16px;
        color: rgba(248, 250, 252, 0.9);
    }

    .player-doc-badge {
        text-align: right;
    }

    .player-doc-badge span {
        font-size: 12px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.8);
    }

    .player-doc-badge strong {
        display: block;
        font-size: 26px;
        margin-top: 4px;
    }

    .player-doc-body {
        padding: 32px;
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .player-doc-profile {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 32px;
        align-items: stretch;
    }

    .player-doc-photo {
        background: radial-gradient(circle at top, #eef2ff, #e0e7ff);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border: 1px dashed #c7d2fe;
    }

    .player-doc-photo img {
        width: 180px;
        height: 180px;
        border-radius: 999px;
        object-fit: cover;
        border: 6px solid #ffffff;
        box-shadow: 0 12px 30px rgba(79, 70, 229, 0.25);
        background: #ffffff;
    }

    .player-doc-photo span {
        margin-top: 12px;
        font-size: 13px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #6366f1;
        font-weight: 600;
    }

    .player-doc-details {
        background: linear-gradient(180deg, #f8fafc, #ffffff);
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 24px 28px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
    }

    .player-doc-detail .label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3em;
        color: #94a3b8;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .player-doc-detail .value {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
    }

    .player-doc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    .player-doc-tile {
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        position: relative;
    }

    .player-doc-tile::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 20px;
        border: 1px solid rgba(79, 70, 229, 0.2);
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .player-doc-tile:hover::before {
        opacity: 1;
    }

    .player-doc-tile h4 {
        margin: 0;
        font-size: 14px;
        letter-spacing: 0.24em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .player-doc-tile .value {
        font-size: 21px;
        font-weight: 700;
        margin-top: 8px;
        color: #0f172a;
    }

    .player-doc-tile .meta {
        margin-top: 6px;
        font-size: 13px;
        color: #6366f1;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .player-doc-columns {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .player-doc-list {
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        background: #f8fafc;
    }

    .player-doc-list h5 {
        margin: 0 0 14px;
        font-size: 14px;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .player-doc-list ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .player-doc-list li {
        padding: 12px 14px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        color: #1e293b;
    }

    .player-doc-footer {
        padding: 20px 32px 32px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #94a3b8;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    @media (max-width: 768px) {
        .player-doc-card {
            border-radius: 20px;
        }

        .player-doc-header,
        .player-doc-body,
        .player-doc-footer {
            padding: 20px;
        }

        .player-doc-profile {
            grid-template-columns: 1fr;
        }

        .player-doc-photo {
            padding: 18px;
        }
    }
</style>
