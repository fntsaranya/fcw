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

<section class="section" style="padding-top: 0;">
    <div class="container">
        <div style="text-align: center;">
            <h2 style="margin-bottom: 2.5rem; color: var(--primary-color);">Patient Journeys</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
                <div class="glass-card" style="text-align: left;">
                    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem;">
                        <div style="width: 50px; height: 50px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fas fa-quote-left" style="color: var(--primary-color);"></i></div>
                        <div>
                            <p style="font-weight: 600; color: var(--primary-color); margin: 0;">Sarah M.</p>
                            <p style="font-size: 0.8rem; color: #666; margin: 0;">Gut Healing Program</p>
                        </div>
                    </div>
                    <p style="font-size: 1.1rem; font-style: italic; line-height: 1.6;">"I finally got my life back! After years of struggling with fatigue and brain fog, we found the missing piece."</p>
                </div>

                <div class="glass-card" style="text-align: left;">
                    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem;">
                        <div style="width: 50px; height: 50px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fas fa-quote-left" style="color: var(--primary-color);"></i></div>
                        <div>
                            <p style="font-weight: 600; color: var(--primary-color); margin: 0;">David K.</p>
                            <p style="font-size: 0.8rem; color: #666; margin: 0;">Autoimmune Recovery</p>
                        </div>
                    </div>
                    <p style="font-size: 1.1rem; font-style: italic; line-height: 1.6;">"We identified the root causes and hidden inflammation. I'm now pain-free and thriving again."</p>
                </div>

                <div class="glass-card" style="text-align: left;">
                    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem;">
                        <div style="width: 50px; height: 50px; background: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fas fa-quote-left" style="color: var(--primary-color);"></i></div>
                        <div>
                            <p style="font-weight: 600; color: var(--primary-color); margin: 0;">Elena R.</p>
                            <p style="font-size: 0.8rem; color: #666; margin: 0;">Hormonal Reset</p>
                        </div>
                    </div>
                    <p style="font-size: 1.1rem; font-style: italic; line-height: 1.6;">"The science-driven approach made all the difference. I feel empowered."</p>
                </div>
            </div>
        </div>
    </div>
</section>
