<section class="hero-section" style="position: relative; min-height: 90vh; display: flex; align-items: center; justify-content: center; text-align: center; overflow: hidden; padding-top: 2rem;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: url('<?= e(asset('images/hero-bg.jpg')) ?>') no-repeat center center/cover; opacity: 0.8; z-index: -1;"></div>

    <div class="container">
        <div class="glass-card" style="max-width: 900px; margin: 0 auto; background: rgba(255, 250, 245, 0.5); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.7); padding: 4rem 2rem;">
            <div style="background: white; width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem auto; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <img src="<?= e(asset('images/logo.webp')) ?>" alt="FCW" style="height: 80px;">
            </div>

            <h1 style="margin-bottom: 0.5rem; color: var(--primary-color); font-size: 3.5rem;">Functional Chronic Wellness</h1>
            <h2 style="font-family: var(--font-body); font-weight: 300; font-size: 2rem; color: var(--text-dark); margin-bottom: 3rem;">Let's Find the Missing Piece.</h2>

            <a href="/assessment" class="btn-primary" style="font-size: 1.3rem; padding: 1rem 3rem; background: var(--primary-color); border-radius: 50px; text-transform: uppercase; letter-spacing: 1px;">Start Your Health Assessment</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="text-align: center;">
        <h2 style="margin-bottom: 2.5rem; color: var(--primary-color);">Why Functional Chronic Wellness</h2>
        <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div class="glass-card" style="background: white; border: none; padding: 2.5rem 2rem; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: left;">
                <div style="margin-bottom: 1.5rem;"><i class="fas fa-user-md fa-3x" style="color: var(--primary-color);"></i></div>
                <h3 style="color: var(--primary-color); margin-bottom: 1rem; font-size: 1.5rem;">Personalized Care</h3>
                <p style="color: var(--text-dark); opacity: 0.8; line-height: 1.6;">Not protocols. We treat the unique individual, not just the disease label.</p>
            </div>

            <div class="glass-card" style="background: white; border: none; padding: 2.5rem 2rem; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: left;">
                <div style="margin-bottom: 1.5rem;"><i class="fas fa-microscope fa-3x" style="color: var(--primary-color);"></i></div>
                <h3 style="color: var(--primary-color); margin-bottom: 1rem; font-size: 1.5rem;">Science-Driven</h3>
                <p style="color: var(--text-dark); opacity: 0.8; line-height: 1.6;">Advanced lab testing and evidence-based analysis to find root causes.</p>
            </div>

            <div class="glass-card" style="background: white; border: none; padding: 2.5rem 2rem; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: left;">
                <div style="margin-bottom: 1.5rem;"><i class="fas fa-leaf fa-3x" style="color: var(--primary-color);"></i></div>
                <h3 style="color: var(--primary-color); margin-bottom: 1rem; font-size: 1.5rem;">Sustainable Results</h3>
                <p style="color: var(--text-dark); opacity: 0.8; line-height: 1.6;">Long-term healing roadmaps that empower you to take control of your health.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 style="margin-bottom: 3rem;">Symptom vs. Root Cause</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
            <div class="glass-card" style="background: linear-gradient(135deg, #6B9080 0%, #4A6A5D 100%); color: white; border: none;">
                <h3 style="color: white; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 1rem;">Symptoms</h3>
                <ul style="list-style: none; font-size: 1.2rem; line-height: 2;">
                    <li><i class="fas fa-exclamation-circle" style="opacity: 0.8;"></i> Fatigue & Burnout</li>
                    <li><i class="fas fa-exclamation-circle" style="opacity: 0.8;"></i> Bloating & Indigestion</li>
                    <li><i class="fas fa-exclamation-circle" style="opacity: 0.8;"></i> Brain Fog</li>
                    <li><i class="fas fa-exclamation-circle" style="opacity: 0.8;"></i> Hormonal Acne</li>
                </ul>
            </div>

            <div class="glass-card" style="background: linear-gradient(135deg, #E0C097 0%, #D4B48C 100%); color: var(--text-dark); border: none;">
                <h3 style="color: var(--text-dark); border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 1rem;">Root Causes</h3>
                <ul style="list-style: none; font-size: 1.2rem; line-height: 2;">
                    <li><i class="fas fa-check-circle" style="color: var(--primary-color);"></i> Gut Microbiome Imbalance</li>
                    <li><i class="fas fa-check-circle" style="color: var(--primary-color);"></i> Nutrient Deficiencies</li>
                    <li><i class="fas fa-check-circle" style="color: var(--primary-color);"></i> Hidden Inflammation</li>
                    <li><i class="fas fa-check-circle" style="color: var(--primary-color);"></i> Chronic Stress Response</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top: 1rem; padding-bottom: 5rem;">
    <div class="container">
        <div style="text-align: center; max-width: 800px; margin: 0 auto 3rem auto;">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(47, 79, 79, 0.08); color: var(--primary-color); padding: 0.45rem 1.2rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 1rem;">
                <i class="fas fa-star" style="color: #f59e0b;"></i> Real Transformations · Measurable Clinical Outcomes
            </div>
            <h2 style="margin-bottom: 1rem; color: var(--primary-color); font-size: 2.6rem;">Patient Journeys</h2>
            <p style="color: #4b5563; font-size: 1.15rem; line-height: 1.6; margin: 0;">
                Real experiences from clients who transformed their health through personalized functional nutrition, root-cause diagnostics, and sustainable lifestyle changes.
            </p>
        </div>

        <div class="patient-journeys-grid">
            <!-- Testimonial 1 -->
            <div class="journey-card">
                <div class="journey-card-top">
                    <span class="journey-tag journey-tag-teal">
                        <i class="fas fa-heart-pulse"></i> Client journey · 6 months
                    </span>
                    <div class="journey-stars" aria-label="5 out of 5 stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>

                <h3 class="journey-card-title">Improved blood sugar control</h3>

                <!-- Key Metrics / Highlights Strip -->
                <div class="journey-metrics-strip">
                    <div class="metric-badge">
                        <span class="metric-kicker">HbA1c</span>
                        <span class="metric-stat">8.9% <i class="fas fa-arrow-right metric-arrow"></i> 6.8%</span>
                    </div>
                    <div class="metric-badge">
                        <span class="metric-kicker">Weight</span>
                        <span class="metric-stat">109 kg <i class="fas fa-arrow-right metric-arrow"></i> 86 kg</span>
                        <span class="metric-delta">(-23 kg)</span>
                    </div>
                </div>

                <div class="journey-quote-wrap">
                    <i class="fas fa-quote-left journey-quote-icon"></i>
                    <p class="journey-quote-text">
                        “My HbA1c was 8.9%, and I was taking insulin three times a day. With personalized nutrition guidance and lifestyle changes, my HbA1c improved to 7.2% after three months and 6.8% after six months. My weight also reduced from 109 kg to 86 kg.”
                    </p>
                </div>

                <div class="journey-client-footer">
                    <div class="client-avatar client-avatar-teal">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="client-info">
                        <span class="client-name">Diabetes Reversal Journey</span>
                        <span class="client-meta">Personalized Nutrition & Insulin Reduction</span>
                    </div>
                    <span class="verified-pill" title="Verified Client Journey">
                        <i class="fas fa-check-circle"></i> Verified
                    </span>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="journey-card">
                <div class="journey-card-top">
                    <span class="journey-tag journey-tag-amber">
                        <i class="fas fa-seedling"></i> Personalized nutrition support
                    </span>
                    <div class="journey-stars" aria-label="5 out of 5 stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>

                <h3 class="journey-card-title">A journey toward better metabolic health</h3>

                <!-- Key Metrics / Highlights Strip -->
                <div class="journey-metrics-strip">
                    <div class="metric-badge">
                        <span class="metric-kicker">Habits</span>
                        <span class="metric-stat">Consistent Daily Choices</span>
                    </div>
                    <div class="metric-badge">
                        <span class="metric-kicker">Monitoring</span>
                        <span class="metric-stat">Active Blood Sugar Control</span>
                    </div>
                </div>

                <div class="journey-quote-wrap">
                    <i class="fas fa-quote-left journey-quote-icon"></i>
                    <p class="journey-quote-text">
                        “I learned how to make better food choices, build consistent lifestyle habits, and monitor my blood sugar. The personalized approach helped me work toward better diabetes management alongside my medical care.”
                    </p>
                </div>

                <div class="journey-client-footer">
                    <div class="client-avatar client-avatar-amber">
                        <i class="fas fa-apple-whole"></i>
                    </div>
                    <div class="client-info">
                        <span class="client-name">Metabolic Health Client</span>
                        <span class="client-meta">Collaborative Care & Lifestyle Habit Building</span>
                    </div>
                    <span class="verified-pill" title="Verified Client Journey">
                        <i class="fas fa-check-circle"></i> Verified
                    </span>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="journey-card">
                <div class="journey-card-top">
                    <span class="journey-tag journey-tag-purple">
                        <i class="fas fa-chart-line"></i> Client success story · 6 months
                    </span>
                    <div class="journey-stars" aria-label="5 out of 5 stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>

                <h3 class="journey-card-title">TSH improvement</h3>

                <!-- Key Metrics / Highlights Strip -->
                <div class="journey-metrics-strip">
                    <div class="metric-badge">
                        <span class="metric-kicker">TSH Level</span>
                        <span class="metric-stat">22 <i class="fas fa-arrow-right metric-arrow"></i> 3.7</span>
                    </div>
                    <div class="metric-badge">
                        <span class="metric-kicker">Weight Loss</span>
                        <span class="metric-stat">-9 kg over 6 Months</span>
                        <span class="metric-delta">(Healthy & Sustained)</span>
                    </div>
                </div>

                <div class="journey-quote-wrap">
                    <i class="fas fa-quote-left journey-quote-icon"></i>
                    <p class="journey-quote-text">
                        “My TSH was 22, and I was struggling with thyroid-related concerns and weight management. With personalized nutrition guidance and lifestyle changes, my TSH improved to 3.7, and I lost 9 kg over six months.”
                    </p>
                </div>

                <div class="journey-client-footer">
                    <div class="client-avatar client-avatar-purple">
                        <i class="fas fa-shield-heart"></i>
                    </div>
                    <div class="client-info">
                        <span class="client-name">Thyroid Recovery Client</span>
                        <span class="client-meta">Root-Cause Nutrition & Weight Management</span>
                    </div>
                    <span class="verified-pill" title="Verified Client Journey">
                        <i class="fas fa-check-circle"></i> Verified
                    </span>
                </div>
            </div>

            <!-- Testimonial 4 -->
            <div class="journey-card">
                <div class="journey-card-top">
                    <span class="journey-tag journey-tag-blue">
                        <i class="fas fa-prescription-bottle-medical"></i> Client success story · Gayathri
                    </span>
                    <div class="journey-stars" aria-label="5 out of 5 stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>

                <h3 class="journey-card-title">A journey toward better thyroid health</h3>

                <!-- Key Metrics / Highlights Strip -->
                <div class="journey-metrics-strip">
                    <div class="metric-badge">
                        <span class="metric-kicker">TSH Level</span>
                        <span class="metric-stat">7.9 <i class="fas fa-arrow-right metric-arrow"></i> 2.5</span>
                    </div>
                    <div class="metric-badge">
                        <span class="metric-kicker">Thyroid Dosage</span>
                        <span class="metric-stat">100 mcg <i class="fas fa-arrow-right metric-arrow"></i> 50 mcg</span>
                        <span class="metric-delta">(50% Dose Cut)</span>
                    </div>
                </div>

                <div class="journey-quote-wrap">
                    <i class="fas fa-quote-left journey-quote-icon"></i>
                    <p class="journey-quote-text">
                        “My TSH was 7.9 while taking 100 mcg of thyroid medication. During my follow-up, my TSH was 2.5, and my doctor reviewed my medication dose, which was reduced to 50 mcg.”
                    </p>
                </div>

                <div class="journey-client-footer">
                    <div class="client-avatar client-avatar-blue">
                        <span>G</span>
                    </div>
                    <div class="client-info">
                        <span class="client-name">Gayathri</span>
                        <span class="client-meta">Thyroid Optimization & Doctor-Supervised Taper</span>
                    </div>
                    <span class="verified-pill" title="Verified Client Journey">
                        <i class="fas fa-check-circle"></i> Verified
                    </span>
                </div>
            </div>
        </div>

        <div style="margin-top: 3.5rem; text-align: center; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(8px); border: 1px solid rgba(47, 79, 79, 0.12); border-radius: 20px; padding: 2.5rem 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <h3 style="color: var(--primary-color); font-size: 1.8rem; margin-bottom: 0.5rem;">Ready for your own breakthrough?</h3>
            <p style="color: #4b5563; font-size: 1.05rem; max-width: 620px; margin: 0 auto 1.5rem auto;">
                Take our comprehensive 2-minute clinical assessment to uncover the hidden root causes of your symptoms.
            </p>
            <a href="/assessment" class="btn-primary" style="padding: 0.95rem 2.8rem; font-size: 1.1rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block;">
                Start Your Health Assessment <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </a>
        </div>
    </div>
</section>

<style>
    .patient-journeys-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }

    @media (max-width: 900px) {
        .patient-journeys-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
    }

    .journey-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 2.2rem;
        box-shadow: 0 10px 30px rgba(47, 79, 79, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease, border-color 0.35s ease;
    }

    .journey-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        opacity: 0.85;
    }

    .journey-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(47, 79, 79, 0.12);
        border-color: rgba(205, 133, 63, 0.45);
    }

    .journey-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .journey-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .journey-tag-teal {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .journey-tag-amber {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .journey-tag-purple {
        background: #faf5ff;
        color: #6b21a8;
        border: 1px solid #e9d5ff;
    }

    .journey-tag-blue {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .journey-stars {
        color: #f59e0b;
        font-size: 0.88rem;
        letter-spacing: 2px;
        display: flex;
    }

    .journey-card-title {
        font-family: var(--font-heading);
        font-size: 1.55rem;
        color: var(--primary-color);
        margin: 0 0 1.1rem 0;
        line-height: 1.3;
    }

    /* Attention-Grabbing Metrics Strip */
    .journey-metrics-strip {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .metric-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.5rem 0.85rem;
        display: inline-flex;
        flex-direction: column;
        gap: 0.15rem;
        flex: 1 1 auto;
        min-width: 140px;
    }

    .metric-kicker {
        font-size: 0.72rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #64748b;
    }

    .metric-stat {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--primary-color);
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .metric-arrow {
        color: var(--accent-color);
        font-size: 0.75rem;
    }

    .metric-delta {
        font-size: 0.75rem;
        font-weight: 700;
        color: #16a34a;
    }

    .journey-quote-wrap {
        position: relative;
        background: #fafaf9;
        border-left: 3px solid var(--accent-color);
        border-radius: 0 12px 12px 0;
        padding: 1.1rem 1.2rem 1.1rem 1.4rem;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .journey-quote-icon {
        position: absolute;
        top: 0.6rem;
        left: 0.4rem;
        font-size: 0.75rem;
        color: var(--accent-color);
        opacity: 0.4;
    }

    .journey-quote-text {
        font-size: 1.02rem;
        line-height: 1.65;
        color: #334155;
        font-style: italic;
        margin: 0;
    }

    .journey-client-footer {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }

    .client-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .client-avatar-teal {
        background: #ecfdf5;
        color: #065f46;
    }

    .client-avatar-amber {
        background: #fffbeb;
        color: #b45309;
    }

    .client-avatar-purple {
        background: #faf5ff;
        color: #7e22ce;
    }

    .client-avatar-blue {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .client-info {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .client-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1e293b;
    }

    .client-meta {
        font-size: 0.8rem;
        color: #64748b;
    }

    .verified-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #16a34a;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        flex-shrink: 0;
    }
</style>
