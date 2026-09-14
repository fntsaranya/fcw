<?php
$contacts = $contacts ?? [];
$enquiryRegistrations = $enquiryRegistrations ?? [];
$activeWebinar = $activeWebinar ?? [];
$assessments = $assessments ?? [];
$blogs = $blogs ?? [];
$appointments = $appointments ?? [];
$booleanFields = $booleanFields ?? [];
$fieldLabels = $fieldLabels ?? [];
$activeTab = $activeTab ?? 'contacts';
?>
<section class="section" style="padding-top: 4rem; padding-bottom: 4rem;">
    <div class="container" style="max-width: 1280px;">
        <div style="display: flex; gap: 1rem; margin-bottom: 2.5rem; justify-content: center; background: rgba(255, 255, 255, 0.5); padding: 0.5rem; border-radius: 50px; border: 1px solid rgba(0, 0, 0, 0.05); width: fit-content; margin-left: auto; margin-right: auto; flex-wrap: wrap;">
            <button onclick="switchTab('contacts')" id="tab-contacts" class="admin-nav-tab" style="padding: 0.8rem 2rem; border: none; cursor: pointer; border-radius: 40px; transition: all 0.3s; display: flex; align-items: center; gap: 0.8rem;">
                <i class="fas fa-envelope" style="font-size: 1.1rem;"></i> Contact List & Enquiries
            </button>
            <button onclick="switchTab('assessments')" id="tab-assessments" class="admin-nav-tab" style="padding: 0.8rem 2rem; border: none; cursor: pointer; border-radius: 40px; transition: all 0.3s; display: flex; align-items: center; gap: 0.8rem;">
                <i class="fas fa-clipboard-list" style="font-size: 1.1rem;"></i> Assessments
            </button>
            <button onclick="switchTab('blogs')" id="tab-blogs" class="admin-nav-tab" style="padding: 0.8rem 2rem; border: none; cursor: pointer; border-radius: 40px; transition: all 0.3s; display: flex; align-items: center; gap: 0.8rem;">
                <i class="fas fa-edit" style="font-size: 1.1rem;"></i> Blogs
            </button>
            <button onclick="switchTab('appointments')" id="tab-appointments" class="admin-nav-tab" style="padding: 0.8rem 2rem; border: none; cursor: pointer; border-radius: 40px; transition: all 0.3s; display: flex; align-items: center; gap: 0.8rem;">
                <i class="fas fa-indian-rupee-sign" style="font-size: 1.1rem;"></i> Appointments
            </button>
        </div>

        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 2rem;">Admin Data Manager</h2>

        <?php if (!empty($inlineErrorMessage)): ?>
            <div style="background: rgba(255, 0, 0, 0.1); color: #d32f2f; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; border: 1px solid #d32f2f; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-exclamation-circle"></i> <?= e((string) $inlineErrorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Webinar Details Management Panel -->
        <div id="webinar-panel" class="glass-card" style="margin-bottom: 2rem;">
            <button id="toggleWebinarForm" style="width: 100%; padding: 1.1rem; background: linear-gradient(135deg, rgba(45, 106, 79, 0.15), rgba(82, 183, 136, 0.25)); border: 1px solid var(--primary-color); border-radius: 10px; color: var(--primary-color); cursor: pointer; font-size: 1.05rem; font-weight: 700; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fas fa-video"></i> Manage Webinar Details & Promotional Banner</span>
                <i class="fas fa-chevron-down" id="toggleWebinarIcon" style="transition: transform 0.3s;"></i>
            </button>

            <form id="webinarForm" action="/admin/webinar/update" method="post" enctype="multipart/form-data" style="display:none; padding-top: 1.5rem; gap: 1.2rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="webinar_title" style="display:block; margin-bottom:0.4rem; font-weight:600;">Webinar Title *</label>
                        <input type="text" id="webinar_title" name="title" value="<?= e((string)($activeWebinar['title'] ?? '')) ?>" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                    </div>
                    <div>
                        <label for="webinar_subtitle" style="display:block; margin-bottom:0.4rem; font-weight:600;">Subtitle / Badge</label>
                        <input type="text" id="webinar_subtitle" name="subtitle" value="<?= e((string)($activeWebinar['subtitle'] ?? '')) ?>" placeholder="e.g. 🦋 FREE LIVE WEBINAR" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="webinar_date" style="display:block; margin-bottom:0.4rem; font-weight:600;">Date *</label>
                        <input type="text" id="webinar_date" name="event_date" value="<?= e((string)($activeWebinar['event_date'] ?? '')) ?>" placeholder="e.g. Sunday, 16th August" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                    </div>
                    <div>
                        <label for="webinar_time" style="display:block; margin-bottom:0.4rem; font-weight:600;">Time *</label>
                        <input type="text" id="webinar_time" name="event_time" value="<?= e((string)($activeWebinar['event_time'] ?? '')) ?>" placeholder="e.g. 10:00 AM – 1:00 PM" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                    </div>
                    <div>
                        <label for="webinar_fee" style="display:block; margin-bottom:0.4rem; font-weight:600;">Registration Fee (INR) *</label>
                        <input type="number" step="0.01" min="0" id="webinar_fee" name="fee_inr" value="<?= e((string)($activeWebinar['fee_inr'] ?? '0.00')) ?>" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                        <small style="color: #64748b; font-size: 0.8rem;">Set 0.00 for free registration, or enter paid amount.</small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="webinar_speaker" style="display:block; margin-bottom:0.4rem; font-weight:600;">Speaker / Host</label>
                        <input type="text" id="webinar_speaker" name="speaker" value="<?= e((string)($activeWebinar['speaker'] ?? '')) ?>" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                    </div>
                    <div>
                        <label for="webinar_venue" style="display:block; margin-bottom:0.4rem; font-weight:600;">Venue / Platform</label>
                        <input type="text" id="webinar_venue" name="venue_platform" value="<?= e((string)($activeWebinar['venue_platform'] ?? 'Live Online')) ?>" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                    </div>
                </div>

                <div>
                    <label for="webinar_whatsapp" style="display:block; margin-bottom:0.4rem; font-weight:600;">Webinar WhatsApp Group Link</label>
                    <input type="url" id="webinar_whatsapp" name="whatsapp_group_link" value="<?= e((string)($activeWebinar['whatsapp_group_link'] ?? '')) ?>" placeholder="https://chat.whatsapp.com/..." style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                    <small style="color: #64748b; font-size: 0.8rem;">Leave blank to use the default WhatsApp group link configured in contacts.</small>
                </div>

                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-weight:600;">Webinar Promotional Banner / Image</label>
                    <?php if (!empty($activeWebinar['image_url'])): ?>
                        <div style="margin-bottom: 0.8rem; display: flex; align-items: center; gap: 1rem; background: #f8fafc; padding: 0.6rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <img src="<?= e((string)$activeWebinar['image_url']) ?>" alt="Banner" style="max-height: 80px; max-width: 160px; border-radius: 6px; object-fit: cover;">
                            <div>
                                <div style="font-size: 0.85rem; color: #1e293b; font-weight: 600;">Current Banner Image</div>
                                <div style="font-size: 0.75rem; color: #64748b;"><?= e((string)$activeWebinar['image_url']) ?></div>
                                <input type="hidden" name="existing_image_url" value="<?= e((string)$activeWebinar['image_url']) ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="banner_image" accept="image/*" style="width:100%; padding:0.6rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15); background: white;">
                    <div style="margin-top: 0.4rem;">
                        <input type="url" name="image_url" placeholder="Or enter direct image URL (optional)" value="<?= e((string)($activeWebinar['image_url'] ?? '')) ?>" style="width:100%; padding:0.6rem 0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15); font-size: 0.9rem;">
                    </div>
                </div>

                <div>
                    <label for="webinar_description" style="display:block; margin-bottom:0.4rem; font-weight:600;">Description & Key Takeaways *</label>
                    <textarea id="webinar_description" name="description" rows="5" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);"><?= e((string)($activeWebinar['description'] ?? '')) ?></textarea>
                </div>

                <div>
                    <label for="webinar_pin" style="display:block; margin-bottom:0.4rem; font-weight:600;">Admin PIN *</label>
                    <input type="password" id="webinar_pin" name="pin" required placeholder="Enter Admin PIN to save" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.15);">
                </div>

                <button type="submit" class="btn-primary" style="width:100%; font-weight: 700; padding: 0.9rem;">
                    <i class="fas fa-save"></i> Save & Update Webinar Details
                </button>
            </form>
        </div>

        <div id="add-contact-panel" class="glass-card" style="margin-bottom: 2rem;">
            <button id="toggleAddContactForm" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.2)); border: 1px solid var(--primary-color); border-radius: 10px; color: var(--primary-color); cursor: pointer; font-size: 1rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fas fa-plus-circle"></i> Add New Contact Submission</span>
                <i class="fas fa-chevron-down" id="toggleContactIcon" style="transition: transform 0.3s;"></i>
            </button>

            <form id="addContactForm" action="/admin/contacts/add" method="post" style="display:none; padding-top: 1.5rem; gap: 1rem;">
                <div><label for="new_name" style="display:block; margin-bottom:0.4rem; font-weight:500;">Name</label><input type="text" id="new_name" name="name" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></div>
                <div><label for="new_email" style="display:block; margin-bottom:0.4rem; font-weight:500;">Email</label><input type="email" id="new_email" name="email" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></div>
                <div><label for="new_phone" style="display:block; margin-bottom:0.4rem; font-weight:500;">Phone</label><input type="text" id="new_phone" name="phone" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></div>
                <div><label for="new_message" style="display:block; margin-bottom:0.4rem; font-weight:500;">Message</label><textarea id="new_message" name="message" rows="4" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></textarea></div>
                <div><label for="new_pin" style="display:block; margin-bottom:0.4rem; font-weight:500;">Admin PIN</label><input type="password" id="new_pin" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></div>
                <button type="submit" class="btn-primary" style="width:100%;">Add Contact</button>
            </form>
        </div>

        <div id="add-blog-panel" class="glass-card" style="margin-bottom: 2rem; display:none;">
            <button id="toggleAddBlogForm" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, rgba(96, 165, 250, 0.2), rgba(16, 185, 129, 0.2)); border: 1px solid var(--primary-color); border-radius: 10px; color: var(--primary-color); cursor: pointer; font-size: 1rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fas fa-plus-circle"></i> Add New Blog Post</span>
                <i class="fas fa-chevron-down" id="toggleBlogIcon" style="transition: transform 0.3s;"></i>
            </button>

            <form id="addBlogForm" action="/admin/blogs/add" method="post" style="display:none; padding-top: 1.5rem; gap: 1rem;">
                <div><label for="new_blog_title" style="display:block; margin-bottom:0.4rem; font-weight:500;">Title</label><input type="text" id="new_blog_title" name="title" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></div>
                <div><label for="new_blog_image_url" style="display:block; margin-bottom:0.4rem; font-weight:500;">Image URL (optional)</label><input type="url" id="new_blog_image_url" name="image_url" placeholder="https://example.com/image.jpg" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></div>
                <div><label for="new_blog_content" style="display:block; margin-bottom:0.4rem; font-weight:500;">Content</label><textarea id="new_blog_content" name="content" rows="8" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></textarea></div>
                <div><label for="new_blog_pin" style="display:block; margin-bottom:0.4rem; font-weight:500;">Admin PIN</label><input type="password" id="new_blog_pin" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid rgba(0,0,0,0.1);"></div>
                <button type="submit" class="btn-primary" style="width:100%;">Add Blog Post</button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 0.5rem;">Webinar Registrations</div>
                <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 800; line-height: 1;"><?= (int) ($totalEnquiries ?? count($enquiryRegistrations)) ?></div>
            </div>
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 0.5rem;">Total Assessments</div>
                <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 800; line-height: 1;"><?= (int) ($totalAssessments ?? 0) ?></div>
            </div>
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 0.5rem;">Total Blogs in DB</div>
                <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 800; line-height: 1;"><?= (int) ($totalBlogs ?? 0) ?></div>
            </div>
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 0.5rem;">Total Appointments</div>
                <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 800; line-height: 1;"><?= (int) ($totalAppointments ?? count($appointments)) ?></div>
            </div>
        </div>

        <div id="contacts-content">
            <!-- Webinar & Enquiry Registrations Table -->
            <div class="glass-card" style="margin-bottom: 2.5rem; padding: 1.5rem; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="color: var(--primary-color); margin: 0 0 0.2rem 0; font-size: 1.25rem;">
                            <i class="fas fa-users"></i> Webinar & Enquiry Registrations
                        </h3>
                        <p style="color: #64748b; margin: 0; font-size: 0.9rem;">Live registrations received via the Register for Enquiry page with payment tracking.</p>
                    </div>
                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <input
                            type="text"
                            id="searchEnquiries"
                            placeholder="🔍 Search registrations..."
                            style="padding: 0.5rem 0.9rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.88rem; min-width: 220px;"
                            oninput="filterAdminTable('enquiryTable', this.value)"
                        >
                        <button
                            type="button"
                            onclick="exportTableToCSV('enquiryTable', 'fcw_webinar_registrations.csv')"
                            style="padding: 0.5rem 0.9rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-weight: 600; cursor: pointer; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.4rem;"
                        >
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                        <span style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color); font-weight: 700; padding: 0.4rem 0.9rem; border-radius: 20px; font-size: 0.85rem;">
                            Total: <?= count($enquiryRegistrations) ?>
                        </span>
                    </div>
                </div>

                <div class="admin-table-scroll-container">
                    <?php if (empty($enquiryRegistrations)): ?>
                        <div style="text-align:center; padding:3rem; color:#64748b;">No webinar registrations recorded yet.</div>
                    <?php else: ?>
                        <table id="enquiryTable" class="admin-data-table">
                            <thead>
                                <tr>
                                    <th style="padding:0.9rem 0.8rem; text-align:left;">Ref / ID</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:left;">Participant</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:left;">Contact Info</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:left;">Webinar</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:left;">Amount</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:center;">Payment Status</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:left;">Payment / Order ID</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:left;">Reg Date</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:center;">WhatsApp</th>
                                    <th style="padding:0.9rem 0.8rem; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enquiryRegistrations as $reg): ?>
                                    <?php
                                        $pStatus = (string) ($reg['payment_status'] ?? 'pending_payment');
                                        $badgeBg = '#fef3c7'; $badgeColor = '#d97706'; $badgeText = 'Pending';
                                        if ($pStatus === 'payment_verified') {
                                            $badgeBg = '#dcfce7'; $badgeColor = '#15803d'; $badgeText = 'Paid';
                                        } elseif ($pStatus === 'free_registered') {
                                            $badgeBg = '#e0f2fe'; $badgeColor = '#0369a1'; $badgeText = 'Free';
                                        } elseif ($pStatus === 'payment_failed') {
                                            $badgeBg = '#fee2e2'; $badgeColor = '#b91c1c'; $badgeText = 'Failed';
                                        }

                                        $amountVal = (float) ($reg['amount_inr'] ?? 0);
                                        $amtStr = $amountVal > 0 ? '₹' . number_format($amountVal, 2) : 'Free';
                                    ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding:0.8rem; font-weight:700; color:var(--primary-color);">
                                            <?= e((string)$reg['registration_reference']) ?>
                                            <div style="font-size:0.75rem; color:#94a3b8; font-weight:normal;">#<?= (int)$reg['id'] ?></div>
                                        </td>
                                        <td style="padding:0.8rem; font-weight:600; color:#1e293b;">
                                            <?= e((string)$reg['name']) ?>
                                        </td>
                                        <td style="padding:0.8rem;">
                                            <div><i class="fas fa-envelope" style="color:#94a3b8; font-size:0.8rem;"></i> <?= e((string)$reg['email']) ?></div>
                                            <div style="color:#64748b; font-size:0.85rem;"><i class="fas fa-phone" style="color:#94a3b8; font-size:0.8rem;"></i> <?= e((string)$reg['phone']) ?></div>
                                        </td>
                                        <td style="padding:0.8rem; max-width:200px;">
                                            <div style="font-weight:600; color:#334155;"><?= e((string)$reg['webinar_title']) ?></div>
                                        </td>
                                        <td style="padding:0.8rem; font-weight:700; color:#0f172a;">
                                            <?= e($amtStr) ?>
                                        </td>
                                        <td style="padding:0.8rem; text-align:center;">
                                            <span style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; padding: 0.3rem 0.75rem; border-radius: 20px; font-weight: 700; font-size: 0.8rem; display: inline-block;">
                                                <?= $badgeText ?>
                                            </span>
                                            <?php if (!empty($reg['failure_reason']) && $pStatus === 'payment_failed'): ?>
                                                <div style="font-size:0.72rem; color:#b91c1c; margin-top:0.2rem; max-width:140px;" title="<?= e((string)$reg['failure_reason']) ?>">
                                                    <?= e(str_limit((string)$reg['failure_reason'], 30)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:0.8rem; font-family:monospace; font-size:0.8rem; color:#475569;">
                                            <?php if (!empty($reg['gateway_payment_id'])): ?>
                                                <div title="Payment ID"><i class="fas fa-receipt" style="color:#10b981;"></i> <?= e((string)$reg['gateway_payment_id']) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($reg['gateway_order_id'])): ?>
                                                <div style="color:#94a3b8;" title="Order ID"><?= e((string)$reg['gateway_order_id']) ?></div>
                                            <?php endif; ?>
                                            <?php if (empty($reg['gateway_payment_id']) && empty($reg['gateway_order_id'])): ?>
                                                <span style="color:#cbd5e1;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:0.8rem; font-size:0.85rem; color:#64748b; white-space:nowrap;">
                                            <?= e(date('M d, Y H:i', strtotime((string)$reg['created_at']))) ?>
                                        </td>
                                        <td style="padding:0.8rem; text-align:center;">
                                            <?php if (!empty($reg['whatsapp_redirected'])): ?>
                                                <span style="color: #16a34a; font-weight: 600; font-size: 0.85rem;" title="Joined / Redirected to WhatsApp Group">
                                                    <i class="fab fa-whatsapp" style="font-size:1.1rem;"></i> Joined
                                                </span>
                                            <?php else: ?>
                                                <span style="color: #94a3b8; font-size: 0.85rem;">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:0.8rem; text-align:center; white-space:nowrap;">
                                            <button class="delete-enquiry-btn" data-id="<?= (int)$reg['id'] ?>" style="padding:0.5rem 0.8rem; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; color:#b91c1c; cursor:pointer; font-size:0.85rem;">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- General Contact Form Submissions Table -->
            <div class="glass-card" style="padding: 1.5rem; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="color: var(--primary-color); margin: 0; font-size: 1.15rem;">
                        <i class="fas fa-envelope-open-text"></i> General Contact Submissions (<?= count($contacts) ?>)
                    </h3>
                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <input
                            type="text"
                            placeholder="🔍 Search contacts..."
                            style="padding: 0.5rem 0.9rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.88rem; min-width: 200px;"
                            oninput="filterAdminTable('generalContactsTable', this.value)"
                        >
                        <button
                            type="button"
                            onclick="exportTableToCSV('generalContactsTable', 'fcw_general_contacts.csv')"
                            style="padding: 0.5rem 0.9rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-weight: 600; cursor: pointer; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.4rem;"
                        >
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                    </div>
                </div>
                <div class="admin-table-scroll-container">
                    <?php if (empty($contacts)): ?>
                        <div style="text-align:center; padding:2rem; color:#64748b;">No general contact submissions yet.</div>
                    <?php else: ?>
                        <table id="generalContactsTable" class="admin-data-table">
                            <thead>
                                <tr>
                                    <th style="padding:0.8rem; text-align:left;">ID</th>
                                    <th style="padding:0.8rem; text-align:left;">Name</th>
                                    <th style="padding:0.8rem; text-align:left;">Email</th>
                                    <th style="padding:0.8rem; text-align:left;">Phone</th>
                                    <th style="padding:0.8rem; text-align:left;">Message</th>
                                    <th style="padding:0.8rem; text-align:left;">Date</th>
                                    <th style="padding:0.8rem; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contacts as $contactRow): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding:0.8rem; font-weight:700; color:var(--primary-color);">#<?= (int) $contactRow['id'] ?></td>
                                        <td style="padding:0.8rem;"><?= e((string) $contactRow['name']) ?></td>
                                        <td style="padding:0.8rem;"><?= e((string) $contactRow['email']) ?></td>
                                        <td style="padding:0.8rem;"><?= e((string) ($contactRow['phone'] ?: '-')) ?></td>
                                        <td style="padding:0.8rem;"><?= e(str_limit((string) $contactRow['message'], 90)) ?></td>
                                        <td style="padding:0.8rem;"><?= e(date('M d, Y H:i', strtotime((string) $contactRow['created_at']))) ?></td>
                                        <td style="padding:0.8rem; text-align:center; white-space:nowrap;">
                                            <button class="edit-btn" data-id="<?= (int) $contactRow['id'] ?>" data-name="<?= e((string) $contactRow['name']) ?>" data-email="<?= e((string) $contactRow['email']) ?>" data-phone="<?= e((string) $contactRow['phone']) ?>" data-message="<?= e((string) $contactRow['message']) ?>" style="padding:0.45rem 0.8rem; margin-right:0.3rem; background:#e0f2fe; border:1px solid #bae6fd; border-radius:6px; color:#0369a1; cursor:pointer;"><i class="fas fa-edit"></i> Edit</button>
                                            <button class="delete-btn" data-id="<?= (int) $contactRow['id'] ?>" style="padding:0.45rem 0.8rem; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; color:#b91c1c; cursor:pointer;"><i class="fas fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="assessments-content" style="display:none;">
            <div class="glass-card" style="padding: 1.5rem; background:white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="color: var(--accent-color); margin: 0; font-size: 1.15rem;">
                        <i class="fas fa-heartbeat"></i> Health Assessments (<?= count($assessments) ?>)
                    </h3>
                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <input
                            type="text"
                            placeholder="🔍 Search assessments..."
                            style="padding: 0.5rem 0.9rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.88rem; min-width: 200px;"
                            oninput="filterAdminTable('assessmentsTable', this.value)"
                        >
                        <button
                            type="button"
                            onclick="exportTableToCSV('assessmentsTable', 'fcw_health_assessments.csv')"
                            style="padding: 0.5rem 0.9rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-weight: 600; cursor: pointer; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.4rem;"
                        >
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                    </div>
                </div>
                <div class="admin-table-scroll-container">
                    <?php if (empty($assessments)): ?>
                        <div style="text-align:center; padding:3rem; color:#64748b;">No assessments yet.</div>
                    <?php else: ?>
                        <table id="assessmentsTable" class="admin-data-table accent-header">
                            <thead>
                                <tr>
                                    <th style="padding:1rem; text-align:left;">ID</th>
                                    <th style="padding:1rem; text-align:left;">Name</th>
                                    <th style="padding:1rem; text-align:left;">Email</th>
                                    <th style="padding:1rem; text-align:left;">Phone</th>
                                    <th style="padding:1rem; text-align:left;">Result</th>
                                    <th style="padding:1rem; text-align:left;">Score</th>
                                    <th style="padding:1rem; text-align:left;">Date</th>
                                    <th style="padding:1rem; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assessments as $assessment): ?>
                                    <?php
                                        $score = (int) $assessment['score'];
                                        $badgeClass = $score >= 13 ? 'badge-high' : ($score >= 6 ? 'badge-moderate' : 'badge-low');
                                    ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding:1rem; font-weight:700; color:var(--primary-color);">#<?= (int) $assessment['id'] ?></td>
                                        <td style="padding:1rem;"><?= e((string) $assessment['full_name']) ?></td>
                                        <td style="padding:1rem;"><?= e((string) $assessment['email']) ?></td>
                                        <td style="padding:1rem;"><?= e((string) $assessment['phone']) ?></td>
                                        <td style="padding:1rem;"><span class="badge <?= e($badgeClass) ?>"><?= e((string) $assessment['interpretation']) ?></span></td>
                                        <td style="padding:1rem; font-weight:700;"><?= $score ?></td>
                                        <td style="padding:1rem;"><?= e(date('M d, Y', strtotime((string) $assessment['created_at']))) ?></td>
                                        <td style="padding:1rem; text-align:center; white-space:nowrap;">
                                            <button class="edit-assessment-btn"
                                                data-id="<?= (int) $assessment['id'] ?>"
                                                data-full_name="<?= e((string) $assessment['full_name']) ?>"
                                                data-email="<?= e((string) $assessment['email']) ?>"
                                                data-phone="<?= e((string) $assessment['phone']) ?>"
                                                data-score="<?= $score ?>"
                                                data-interpretation="<?= e((string) $assessment['interpretation']) ?>"
                                                <?php foreach ($booleanFields as $field): ?>
                                                    data-<?= e($field) ?>="<?= !empty($assessment[$field]) ? 'true' : 'false' ?>"
                                                <?php endforeach; ?>
                                                style="padding:0.55rem 0.9rem; margin-right:0.3rem; background:#e0f2fe; border:1px solid #bae6fd; border-radius:6px; color:#0369a1; cursor:pointer;">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="delete-assessment-btn" data-id="<?= (int) $assessment['id'] ?>" style="padding:0.55rem 0.9rem; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; color:#b91c1c; cursor:pointer;"><i class="fas fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="blogs-content" style="display:none;">
            <div class="glass-card" style="padding: 1.5rem; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="color: #3b82f6; margin: 0; font-size: 1.15rem;">
                        <i class="fas fa-newspaper"></i> Blog Posts (<?= count($blogs) ?>)
                    </h3>
                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <input
                            type="text"
                            placeholder="🔍 Search blogs..."
                            style="padding: 0.5rem 0.9rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.88rem; min-width: 200px;"
                            oninput="filterAdminTable('blogsTable', this.value)"
                        >
                    </div>
                </div>
                <div class="admin-table-scroll-container">
                    <?php if (empty($blogs)): ?>
                        <div style="text-align:center; padding:3rem; color:#64748b;">No blog posts yet.</div>
                    <?php else: ?>
                        <table id="blogsTable" class="admin-data-table blue-header">
                            <thead>
                                <tr>
                                    <th style="padding:1rem; text-align:left;">ID</th>
                                    <th style="padding:1rem; text-align:left;">Title</th>
                                    <th style="padding:1rem; text-align:left;">Summary</th>
                                    <th style="padding:1rem; text-align:left;">Image</th>
                                    <th style="padding:1rem; text-align:left;">Date</th>
                                    <th style="padding:1rem; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogs as $blog): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding:1rem; font-weight:700; color:var(--primary-color);">#<?= (int) $blog['id'] ?></td>
                                        <td style="padding:1rem; font-weight:600;"><?= e((string) $blog['title']) ?></td>
                                        <td style="padding:1rem;"><?= e(str_limit((string) $blog['content'], 120)) ?></td>
                                        <td style="padding:1rem;">
                                            <?php if (!empty($blog['image_url'])): ?>
                                                <a href="<?= e((string) $blog['image_url']) ?>" target="_blank" rel="noopener" style="color:#2563eb; text-decoration:underline;">Open</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:1rem;"><?= e(date('M d, Y H:i', strtotime((string) $blog['created_at']))) ?></td>
                                        <td style="padding:1rem; text-align:center; white-space:nowrap;">
                                            <button
                                                class="edit-blog-btn"
                                                data-id="<?= (int) $blog['id'] ?>"
                                                data-title="<?= e((string) $blog['title']) ?>"
                                                data-image_url="<?= e((string) ($blog['image_url'] ?? '')) ?>"
                                                data-content="<?= e((string) $blog['content']) ?>"
                                                style="padding:0.55rem 0.9rem; margin-right:0.3rem; background:#e0f2fe; border:1px solid #bae6fd; border-radius:6px; color:#0369a1; cursor:pointer;"
                                            >
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="delete-blog-btn" data-id="<?= (int) $blog['id'] ?>" style="padding:0.55rem 0.9rem; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; color:#b91c1c; cursor:pointer;"><i class="fas fa-trash"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="appointments-content" style="display:none;">
            <div class="glass-card" style="margin-bottom:1rem; background:white; padding: 1.2rem;">
                <p style="margin:0; color:#475569;">Appointments booked through the paid flow appear here with Razorpay order, payment, gateway, and booking status.</p>
            </div>
            <div class="glass-card" style="padding: 1.5rem; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="color: #0f766e; margin: 0; font-size: 1.15rem;">
                        <i class="fas fa-calendar-check"></i> Appointment Bookings (<?= count($appointments) ?>)
                    </h3>
                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <input
                            type="text"
                            placeholder="🔍 Search bookings..."
                            style="padding: 0.5rem 0.9rem; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.88rem; min-width: 200px;"
                            oninput="filterAdminTable('appointmentsTable', this.value)"
                        >
                        <button
                            type="button"
                            onclick="exportTableToCSV('appointmentsTable', 'fcw_appointment_bookings.csv')"
                            style="padding: 0.5rem 0.9rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; color: #166534; font-weight: 600; cursor: pointer; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.4rem;"
                        >
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                    </div>
                </div>
                <div class="admin-table-scroll-container">
                    <?php if (empty($appointments)): ?>
                        <div style="text-align:center; padding:3rem; color:#64748b;">No appointment bookings yet.</div>
                    <?php else: ?>
                        <table id="appointmentsTable" class="admin-data-table teal-header">
                            <thead>
                                <tr>
                                    <th style="padding:0.9rem; text-align:left;">ID</th>
                                    <th style="padding:0.9rem; text-align:left;">Reference</th>
                                    <th style="padding:0.9rem; text-align:left;">Name</th>
                                    <th style="padding:0.9rem; text-align:left;">Contact</th>
                                    <th style="padding:0.9rem; text-align:left;">Preferred Slot</th>
                                    <th style="padding:0.9rem; text-align:left;">Amount</th>
                                    <th style="padding:0.9rem; text-align:left;">Method</th>
                                    <th style="padding:0.9rem; text-align:left;">Razorpay IDs</th>
                                    <th style="padding:0.9rem; text-align:left;">Gateway</th>
                                    <th style="padding:0.9rem; text-align:left;">Booking Status</th>
                                    <th style="padding:0.9rem; text-align:left;">Intake Status</th>
                                    <th style="padding:0.9rem; text-align:left;">Updated</th>
                                    <th style="padding:0.9rem; text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                    <?php
                                        $statusKey = (string) ($appointment['payment_status'] ?? 'pending_payment');
                                        $statusMap = [
                                            'pending_payment' => ['label' => 'Pending', 'bg' => '#fef3c7', 'color' => '#92400e'],
                                            'payment_initiated' => ['label' => 'Initiated', 'bg' => '#dbeafe', 'color' => '#1d4ed8'],
                                            'payment_authorized' => ['label' => 'Authorized', 'bg' => '#dbeafe', 'color' => '#1d4ed8'],
                                            'payment_submitted' => ['label' => 'Submitted', 'bg' => '#dbeafe', 'color' => '#1d4ed8'],
                                            'payment_verified' => ['label' => 'Paid', 'bg' => '#dcfce7', 'color' => '#166534'],
                                            'payment_failed' => ['label' => 'Failed', 'bg' => '#fee2e2', 'color' => '#b91c1c'],
                                            'payment_partially_refunded' => ['label' => 'Partially Refunded', 'bg' => '#f3e8ff', 'color' => '#7e22ce'],
                                            'payment_refunded' => ['label' => 'Refunded', 'bg' => '#e2e8f0', 'color' => '#475569'],
                                            'payment_rejected' => ['label' => 'Rejected', 'bg' => '#fee2e2', 'color' => '#b91c1c'],
                                        ];
                                        $status = $statusMap[$statusKey] ?? $statusMap['pending_payment'];
                                        $slotDate = trim((string) ($appointment['preferred_date'] ?? ''));
                                        $slotTime = trim((string) ($appointment['preferred_time'] ?? ''));
                                        $slotText = $slotDate !== '' ? $slotDate . ($slotTime !== '' ? ' ' . $slotTime : '') : '-';
                                        $updatedValue = $appointment['gateway_updated_at'] ?? $appointment['payment_acknowledged_at'] ?? null;
                                        $updatedText = !empty($updatedValue) ? date('M d, Y H:i', strtotime((string) $updatedValue)) : '-';
                                        $channelKey = strtolower(trim((string) ($appointment['gateway_method'] ?? $appointment['payment_channel'] ?? '')));
                                        $channelLabel = match ($channelKey) {
                                            'netbanking' => 'Netbanking',
                                            'upi' => 'UPI',
                                            default => ($channelKey !== '' ? ucfirst(str_replace('razorpay_', '', $channelKey)) : '-'),
                                        };
                                        $orderId = trim((string) ($appointment['gateway_order_id'] ?? ''));
                                        $paymentId = trim((string) ($appointment['gateway_payment_id'] ?? ''));
                                        $gatewayStatus = trim((string) ($appointment['gateway_payment_status'] ?? $appointment['gateway_order_status'] ?? ''));
                                        $hasIntakeForm = !empty($appointment['has_intake_form']);
                                        $intakeLink = '/patient-intake?ref=' . urlencode((string) ($appointment['booking_reference'] ?? '')) . '&token=' . urlencode((string) ($appointment['ack_token'] ?? ''));
                                    ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9; vertical-align: top;">
                                        <td style="padding:0.85rem; font-weight:700; color:var(--primary-color);">#<?= (int) ($appointment['id'] ?? 0) ?></td>
                                        <td style="padding:0.85rem; font-weight:700;"><?= e((string) ($appointment['booking_reference'] ?? '-')) ?></td>
                                        <td style="padding:0.85rem;"><?= e((string) ($appointment['full_name'] ?? '-')) ?></td>
                                        <td style="padding:0.85rem;">
                                            <div><?= e((string) ($appointment['email'] ?? '-')) ?></div>
                                            <div style="font-size:0.88rem; color:#6b7280;"><?= e((string) ($appointment['phone'] ?? '-')) ?></div>
                                        </td>
                                        <td style="padding:0.85rem;"><?= e($slotText) ?></td>
                                        <td style="padding:0.85rem; font-weight:600;"><?= e((string) ($appointment['currency'] ?? 'INR')) ?> <?= e((string) ($appointment['amount_inr'] ?? '0')) ?></td>
                                        <td style="padding:0.85rem;"><?= e($channelLabel) ?></td>
                                        <td style="padding:0.85rem; font-size:0.82rem;">
                                            <div><?= e($orderId !== '' ? $orderId : '-') ?></div>
                                            <?php if ($paymentId !== ''): ?>
                                                <div style="color:#64748b; margin-top:0.25rem;"><?= e($paymentId) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:0.85rem;"><?= e($gatewayStatus !== '' ? ucfirst(str_replace('_', ' ', $gatewayStatus)) : '-') ?></td>
                                        <td style="padding:0.85rem;"><span style="display:inline-block; padding:0.28rem 0.65rem; border-radius:999px; background:<?= e($status['bg']) ?>; color:<?= e($status['color']) ?>; font-weight:700;"><?= e($status['label']) ?></span></td>
                                        <td style="padding:0.85rem;">
                                            <?php if ($hasIntakeForm): ?>
                                                <span style="display:inline-block; padding:0.28rem 0.65rem; border-radius:999px; background:#dcfce7; color:#166534; font-weight:700; font-size:0.85rem;"><i class="fas fa-check-circle"></i> Submitted</span>
                                            <?php else: ?>
                                                <div style="display:flex; flex-direction:column; gap:0.4rem; align-items:start;">
                                                    <span style="display:inline-block; padding:0.28rem 0.65rem; border-radius:999px; background:#fef3c7; color:#92400e; font-weight:700; font-size:0.85rem;"><i class="fas fa-clock"></i> Pending</span>
                                                    <?php
                                                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                                                        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                                                        $fullUrl = $protocol . $host . $intakeLink;
                                                    ?>
                                                    <button onclick="copyIntakeLink(this, '<?= e(addslashes($fullUrl)) ?>')" style="padding:0.25rem 0.5rem; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:4px; font-size:0.75rem; cursor:pointer; color:#0f766e;"><i class="fas fa-copy"></i> Copy Link</button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding:0.85rem; font-size:0.85rem; color:#64748b; white-space:nowrap;"><?= e($updatedText) ?></td>
                                        <td style="padding:0.85rem; text-align:center; white-space:nowrap;">
                                            <div style="display:inline-flex; gap:0.35rem; align-items:center;">
                                                <a href="/patient-intake/admin/<?= (int) ($appointment['id'] ?? 0) ?>" style="display:inline-flex; align-items:center; gap:0.3rem; padding:0.38rem 0.6rem; border-radius:6px; background:#e0f2fe; color:#0369a1; text-decoration:none; font-size:0.82rem; font-weight:600;"><i class="fas fa-eye"></i> Form</a>
                                                <?php if ($hasIntakeForm): ?>
                                                    <a href="/patient-intake/admin/<?= (int) ($appointment['id'] ?? 0) ?>/download" style="display:inline-flex; align-items:center; gap:0.3rem; padding:0.38rem 0.6rem; border-radius:6px; background:#dcfce7; color:#166534; text-decoration:none; font-size:0.82rem; font-weight:600;"><i class="fas fa-file-pdf"></i> PDF</a>
                                                <?php endif; ?>
                                                <button class="delete-appointment-btn" data-id="<?= (int) ($appointment['id'] ?? 0) ?>" style="padding:0.38rem 0.6rem; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; color:#b91c1c; cursor:pointer; font-size:0.82rem;"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="editContactModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:560px;">
        <h3 style="margin-bottom:1rem;">Edit Contact</h3>
        <form id="editContactForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="text" id="edit_name" name="name" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Name">
            <input type="email" id="edit_email" name="email" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Email">
            <input type="text" id="edit_phone" name="phone" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Phone">
            <textarea id="edit_message" name="message" rows="4" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Message"></textarea>
            <input type="password" id="edit_pin" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" class="btn-primary" style="flex:1;">Update Contact</button>
                <button type="button" onclick="closeEditContactModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteContactModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px;">
        <h3>Delete Contact</h3>
        <p style="margin-bottom:1rem;">Confirm deletion by entering Admin PIN.</p>
        <form id="deleteContactForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" style="flex:1; border:none; border-radius:8px; background:#ef4444; color:white; padding:0.8rem; cursor:pointer;">Delete</button>
                <button type="button" onclick="closeDeleteContactModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="editAssessmentModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:94%; max-width:1000px; max-height:90vh; overflow-y:auto;">
        <h3 style="margin-bottom:1rem;">Edit Assessment</h3>
        <form id="editAssessmentForm" method="post" style="display:grid; gap:1rem;">
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem;">
                <input type="text" id="edit_asm_full_name" name="full_name" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Full name">
                <input type="email" id="edit_asm_email" name="email" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Email">
                <input type="text" id="edit_asm_phone" name="phone" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Phone">
            </div>
            <div style="padding:1rem; border:1px solid #e5e7eb; border-radius:10px; background:#f9fafb;">
                <p><strong>Current Score:</strong> <span id="edit_asm_score">0</span></p>
                <p><strong>Interpretation:</strong> <span id="edit_asm_interpretation">-</span></p>
            </div>
            <div style="max-height:320px; overflow-y:auto; border:1px solid #ddd; border-radius:8px; padding:1rem;">
                <h4 style="margin-top:0;">Clinical questions</h4>
                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap:0.45rem;">
                    <?php foreach ($booleanFields as $field): ?>
                        <label><input type="checkbox" id="chk_<?= e($field) ?>" name="<?= e($field) ?>"> <?= e($fieldLabels[$field] ?? $field) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" class="btn-primary" style="flex:1;">Update Assessment</button>
                <button type="button" onclick="closeEditAssessmentModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteAssessmentModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px;">
        <h3>Delete Assessment</h3>
        <p style="margin-bottom:1rem;">Confirm deletion by entering Admin PIN.</p>
        <form id="deleteAssessmentForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" style="flex:1; border:none; border-radius:8px; background:#ef4444; color:white; padding:0.8rem; cursor:pointer;">Delete</button>
                <button type="button" onclick="closeDeleteAssessmentModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="editBlogModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:92%; max-width:760px; max-height:90vh; overflow-y:auto;">
        <h3 style="margin-bottom:1rem;">Edit Blog Post</h3>
        <form id="editBlogForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="text" id="edit_blog_title" name="title" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Title">
            <input type="url" id="edit_blog_image_url" name="image_url" style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Image URL (optional)">
            <textarea id="edit_blog_content" name="content" rows="10" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Content"></textarea>
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" class="btn-primary" style="flex:1;">Update Blog</button>
                <button type="button" onclick="closeEditBlogModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteBlogModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px;">
        <h3>Delete Blog Post</h3>
        <p style="margin-bottom:1rem;">Confirm deletion by entering Admin PIN.</p>
        <form id="deleteBlogForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" style="flex:1; border:none; border-radius:8px; background:#ef4444; color:white; padding:0.8rem; cursor:pointer;">Delete</button>
                <button type="button" onclick="closeDeleteBlogModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteAppointmentModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px;">
        <h3>Delete Appointment</h3>
        <p style="margin-bottom:1rem;">Confirm deletion by entering Admin PIN.</p>
        <form id="deleteAppointmentForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" style="flex:1; border:none; border-radius:8px; background:#ef4444; color:white; padding:0.8rem; cursor:pointer;">Delete</button>
                <button type="button" onclick="closeDeleteAppointmentModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="downloadPdfModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px;">
        <h3>Download Patient Intake PDF</h3>
        <p style="margin-bottom:1rem;">Enter Admin PIN to securely download the timeline PDF.</p>
        <form id="downloadPdfForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" style="flex:1; border:none; border-radius:8px; background:#0369a1; color:white; padding:0.8rem; cursor:pointer;">Download PDF</button>
                <button type="button" onclick="closeDownloadPdfModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="editIntakeModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px;">
        <h3>Edit Intake Form</h3>
        <p style="margin-bottom:1rem;">Enter Admin PIN to securely edit the intake form.</p>
        <form id="editIntakeForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" style="flex:1; border:none; border-radius:8px; background:#ca8a04; color:white; padding:0.8rem; cursor:pointer;">Edit Form</button>
                <button type="button" onclick="closeEditIntakeModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteEnquiryModal" style="display:none; position:fixed; inset:0; background: rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-card" style="background:white; width:90%; max-width:420px;">
        <h3>Delete Registration Record</h3>
        <p style="margin-bottom:1rem;">Enter Admin PIN to permanently delete this registration record.</p>
        <form id="deleteEnquiryForm" method="post" style="display:grid; gap:0.9rem;">
            <input type="password" name="pin" required style="width:100%; padding:0.8rem; border-radius:8px; border:1px solid #ccc;" placeholder="Admin PIN">
            <div style="display:flex; gap:0.7rem;">
                <button type="submit" style="flex:1; border:none; border-radius:8px; background:#ef4444; color:white; padding:0.8rem; cursor:pointer;">Delete</button>
                <button type="button" onclick="closeDeleteEnquiryModal()" style="flex:1; border:none; border-radius:8px; background:#6b7280; color:white; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
    .badge {
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-block;
    }

    .badge-high { background: #fee2e2; color: #ef4444; }
    .badge-moderate { background: #fef3c7; color: #d97706; }
    .badge-low { background: #dcfce7; color: #22c55e; }

    .admin-table-scroll-container {
        max-height: 520px;
        overflow-x: auto;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        position: relative;
        background: #ffffff;
        box-shadow: inset 0 0 6px rgba(0,0,0,0.02);
    }

    .admin-table-scroll-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .admin-table-scroll-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .admin-table-scroll-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .admin-table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .admin-data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.92rem;
        min-width: 1100px;
    }

    .admin-data-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: var(--primary-color) !important;
        color: #ffffff !important;
        padding: 0.9rem 0.8rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.12);
        border-bottom: 2px solid rgba(0,0,0,0.08);
    }

    .admin-data-table.accent-header thead th {
        background: var(--accent-color) !important;
    }

    .admin-data-table.blue-header thead th {
        background: #3b82f6 !important;
    }

    .admin-data-table.teal-header thead th {
        background: #0f766e !important;
    }

    .admin-data-table tbody tr:hover {
        background: #f8fafc;
    }
</style>

<script>
    function filterAdminTable(tableId, query) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const q = query.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(function (row) {
            const text = row.innerText.toLowerCase();
            if (!q || text.indexOf(q) !== -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function exportTableToCSV(tableId, filename) {
        const table = document.getElementById(tableId);
        if (!table) return;
        let csv = [];
        const rows = table.querySelectorAll('tr');
        rows.forEach(function (row) {
            if (row.style.display === 'none') return;
            let cols = [];
            row.querySelectorAll('th, td').forEach(function (col, index) {
                if (index === row.children.length - 1 && (col.innerText.includes('Action') || col.querySelector('button, a'))) {
                    return;
                }
                let text = col.innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
                cols.push('"' + text + '"');
            });
            csv.push(cols.join(','));
        });
        const blob = new Blob([csv.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', filename || 'export.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    const booleanFields = <?= json_encode(array_values($booleanFields)) ?>;
    const activeTab = <?= json_encode($activeTab) ?>;
    const tabNames = ['contacts', 'assessments', 'blogs', 'appointments'];

    function styleTab(tabName, isActive) {
        const tab = document.getElementById('tab-' + tabName);
        if (!tab) {
            return;
        }

        if (isActive) {
            tab.style.background = 'var(--primary-color)';
            tab.style.color = 'white';
            tab.style.fontWeight = '700';
            tab.style.boxShadow = '0 4px 15px rgba(47, 79, 79, 0.3)';
            return;
        }

        tab.style.background = 'transparent';
        tab.style.color = 'var(--primary-color)';
        tab.style.fontWeight = '600';
        tab.style.boxShadow = 'none';
    }

    function switchTab(tabName) {
        const selected = tabNames.includes(tabName) ? tabName : 'contacts';

        tabNames.forEach(function (name) {
            const content = document.getElementById(name + '-content');
            if (content) {
                content.style.display = name === selected ? 'block' : 'none';
            }
            styleTab(name, name === selected);
        });

        const webinarPanel = document.getElementById('webinar-panel');
        const addContactPanel = document.getElementById('add-contact-panel');
        const addBlogPanel = document.getElementById('add-blog-panel');

        if (webinarPanel) {
            webinarPanel.style.display = selected === 'contacts' ? 'block' : 'none';
        }

        if (addContactPanel) {
            addContactPanel.style.display = selected === 'contacts' ? 'block' : 'none';
        }

        if (addBlogPanel) {
            addBlogPanel.style.display = selected === 'blogs' ? 'block' : 'none';
        }
    }

    function bindToggle(buttonId, formId, iconId) {
        const button = document.getElementById(buttonId);
        const form = document.getElementById(formId);
        const icon = document.getElementById(iconId);

        if (!button || !form || !icon) {
            return;
        }

        button.addEventListener('click', function () {
            const open = form.style.display === 'none' || form.style.display === '';
            form.style.display = open ? 'grid' : 'none';
            icon.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
        });
    }

    bindToggle('toggleWebinarForm', 'webinarForm', 'toggleWebinarIcon');
    bindToggle('toggleAddContactForm', 'addContactForm', 'toggleContactIcon');
    bindToggle('toggleAddBlogForm', 'addBlogForm', 'toggleBlogIcon');

    document.querySelectorAll('.edit-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('edit_name').value = button.getAttribute('data-name') || '';
            document.getElementById('edit_email').value = button.getAttribute('data-email') || '';
            document.getElementById('edit_phone').value = button.getAttribute('data-phone') || '';
            document.getElementById('edit_message').value = button.getAttribute('data-message') || '';
            document.getElementById('editContactForm').action = '/admin/contacts/edit/' + button.getAttribute('data-id');
            document.getElementById('editContactModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.delete-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('deleteContactForm').action = '/admin/contacts/delete/' + button.getAttribute('data-id');
            document.getElementById('deleteContactModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.delete-enquiry-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('deleteEnquiryForm').action = '/admin/enquiries/delete/' + button.getAttribute('data-id');
            document.getElementById('deleteEnquiryModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.edit-assessment-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('edit_asm_full_name').value = button.getAttribute('data-full_name') || '';
            document.getElementById('edit_asm_email').value = button.getAttribute('data-email') || '';
            document.getElementById('edit_asm_phone').value = button.getAttribute('data-phone') || '';
            document.getElementById('edit_asm_score').textContent = button.getAttribute('data-score') || '0';
            document.getElementById('edit_asm_interpretation').textContent = button.getAttribute('data-interpretation') || '-';

            booleanFields.forEach(function (field) {
                const checkbox = document.getElementById('chk_' + field);
                if (checkbox) {
                    checkbox.checked = button.getAttribute('data-' + field) === 'true';
                }
            });

            document.getElementById('editAssessmentForm').action = '/admin/assessments/edit/' + button.getAttribute('data-id');
            document.getElementById('editAssessmentModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.delete-assessment-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('deleteAssessmentForm').action = '/admin/assessments/delete/' + button.getAttribute('data-id');
            document.getElementById('deleteAssessmentModal').style.display = 'flex';
        });
    });

    window.copyIntakeLink = function(btn, url) {
        navigator.clipboard.writeText(url).then(() => {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            btn.style.color = '#166534';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.color = '#0f766e';
            }, 2000);
        });
    };

    document.querySelectorAll('.edit-blog-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('edit_blog_title').value = button.getAttribute('data-title') || '';
            document.getElementById('edit_blog_image_url').value = button.getAttribute('data-image_url') || '';
            document.getElementById('edit_blog_content').value = button.getAttribute('data-content') || '';
            document.getElementById('editBlogForm').action = '/admin/blogs/edit/' + button.getAttribute('data-id');
            document.getElementById('editBlogModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.delete-blog-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('deleteBlogForm').action = '/admin/blogs/delete/' + button.getAttribute('data-id');
            document.getElementById('deleteBlogModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.delete-appointment-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('deleteAppointmentForm').action = '/admin/appointments/delete/' + button.getAttribute('data-id');
            document.getElementById('deleteAppointmentModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.download-pdf-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('downloadPdfForm').action = '/admin/appointments/pdf/' + button.getAttribute('data-id');
            document.getElementById('downloadPdfModal').style.display = 'flex';
        });
    });

    document.querySelectorAll('.edit-intake-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('editIntakeForm').action = '/admin/appointments/intake/edit/' + button.getAttribute('data-id');
            document.getElementById('editIntakeModal').style.display = 'flex';
        });
    });

    function closeEditContactModal() {
        document.getElementById('editContactModal').style.display = 'none';
    }

    function closeDeleteContactModal() {
        document.getElementById('deleteContactModal').style.display = 'none';
    }

    function closeDeleteEnquiryModal() {
        document.getElementById('deleteEnquiryModal').style.display = 'none';
    }

    function closeEditAssessmentModal() {
        document.getElementById('editAssessmentModal').style.display = 'none';
    }

    function closeDeleteAssessmentModal() {
        document.getElementById('deleteAssessmentModal').style.display = 'none';
    }

    function closeEditBlogModal() {
        document.getElementById('editBlogModal').style.display = 'none';
    }

    function closeDeleteBlogModal() {
        document.getElementById('deleteBlogModal').style.display = 'none';
    }

    function closeDeleteAppointmentModal() {
        document.getElementById('deleteAppointmentModal').style.display = 'none';
    }

    function closeDownloadPdfModal() {
        document.getElementById('downloadPdfModal').style.display = 'none';
    }

    function closeEditIntakeModal() {
        document.getElementById('editIntakeModal').style.display = 'none';
    }

    window.addEventListener('click', function (event) {
        ['editContactModal', 'deleteContactModal', 'deleteEnquiryModal', 'editAssessmentModal', 'deleteAssessmentModal', 'editBlogModal', 'deleteBlogModal', 'deleteAppointmentModal', 'downloadPdfModal', 'editIntakeModal'].forEach(function (id) {
            const modal = document.getElementById(id);
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    switchTab(activeTab);
</script>
