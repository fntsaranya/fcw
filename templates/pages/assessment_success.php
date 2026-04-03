<section class="section" style="padding-top: 6rem; min-height: 80vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 700px; text-align: center;">
        <div class="glass-card" style="padding: 4rem 3rem; background: white; border-radius: 30px; border: none; box-shadow: 0 40px 100px rgba(0,0,0,0.08);">
            <div style="margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background: #f0fdf4; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-check-circle fa-3x" style="color: #22c55e;"></i>
                </div>
            </div>

            <h1 style="color: var(--primary-color); margin-bottom: 1rem;">Assessment Submitted!</h1>
            <p style="font-size: 1.2rem; color: #475569; margin-bottom: 3rem;">Thank you, <strong><?= e((string) ($name ?? '')) ?></strong>. We have received your assessment and a practitioner will review it shortly.</p>

            <div style="background: #f8fafc; padding: 2.5rem; border-radius: 20px; margin-bottom: 3rem; border: 1px solid #e2e8f0;">
                <div style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 1rem;">Your Assessment Result</div>
                <div style="font-size: 1.5rem; color: var(--primary-color); font-weight: 800; margin-bottom: 0.5rem;"><?= e((string) ($interpretation ?? '')) ?></div>
                <div style="color: #64748b; font-size: 1rem;">Total "Yes" Responses: <strong><?= (int) ($score ?? 0) ?></strong></div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="/" class="btn-primary" style="padding: 1rem 2rem; border-radius: 50px;">Return Home</a>
                <p style="color: #64748b; font-size: 0.9rem;">Our team will reach out via email at your convenience.</p>
            </div>
        </div>
    </div>
</section>
