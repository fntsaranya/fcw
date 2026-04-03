<section class="section" style="padding-top: 4rem; padding-bottom: 5rem; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
    <div class="container" style="max-width: 800px;">
        <div class="glass-card" style="padding: 3rem; background: white; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.05);">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h1 style="font-size: 2.8rem; color: var(--primary-color); margin-bottom: 1rem;">Health Assessment Form</h1>
                <p style="color: #64748b; font-size: 1.1rem;">Complete this assessment to understand your potential risk of autoimmune involvement.</p>
            </div>

            <?php if (!empty($errorMessage)): ?>
                <div style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; text-align: center; border: 1px solid #dc3545;">
                    <i class="fas fa-exclamation-circle"></i> <?= e($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form action="/assessment" method="post" id="assessmentForm" style="display: grid; gap: 3rem;">
                <div style="padding-bottom: 2rem; border-bottom: 1px solid #f1f5f9;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary-color); display: flex; align-items: center; gap: 0.8rem;">
                        <i class="fas fa-user-circle" style="color: var(--accent-color);"></i> Personal Information
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div>
                            <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name</label>
                            <input type="text" id="name" name="name" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc;">
                        </div>
                        <div>
                            <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                            <input type="email" id="email" name="email" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc;">
                        </div>
                        <div>
                            <label for="phone" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc;">
                        </div>
                    </div>
                </div>

                <?php foreach (($sections ?? []) as $section): ?>
                    <div class="assessment-section" style="padding: 2rem; border-radius: 12px; border: 1px solid #f1f5f9; background: #fff; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <h3 style="margin-bottom: 2rem; color: var(--primary-color); font-size: 1.2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 0.8rem;"><?= e((string) $section['title']) ?></h3>
                        <div style="display: grid; gap: 1.5rem;">
                            <?php foreach (($section['questions'] ?? []) as $question): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                    <label style="color: #334155; font-weight: 500; font-size: 0.95rem;"><?= e((string) $question[0]) ?></label>
                                    <div style="display: flex; gap: 1rem;">
                                        <label class="radio-label">
                                            <input type="radio" name="<?= e((string) $question[1]) ?>" value="yes" required>
                                            <span class="radio-custom">Yes</span>
                                        </label>
                                        <label class="radio-label">
                                            <input type="radio" name="<?= e((string) $question[1]) ?>" value="no" required>
                                            <span class="radio-custom">No</span>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn-primary" style="padding: 1.2rem; font-size: 1.2rem; font-weight: 700; border-radius: 50px; text-transform: uppercase; letter-spacing: 1px; margin-top: 2rem;">
                    Submit Assessment & View Results
                </button>
            </form>
        </div>
    </div>
</section>

<style>
    .assessment-section:hover {
        border-color: var(--primary-color) !important;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05) !important;
    }

    .radio-label input {
        display: none;
    }

    .radio-custom {
        padding: 0.5rem 1.5rem;
        border-radius: 20px;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
        border: 2px solid transparent;
        display: inline-block;
    }

    .radio-label input:checked + .radio-custom {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 6px rgba(47, 79, 79, 0.2);
    }

    .radio-label:hover .radio-custom {
        background: #e2e8f0;
    }
</style>
