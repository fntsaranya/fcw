<?php
$contacts = $contacts ?? [];
$assessments = $assessments ?? [];
$blogs = $blogs ?? [];
$booleanFields = $booleanFields ?? [];
$fieldLabels = $fieldLabels ?? [];
$activeTab = $activeTab ?? 'contacts';
?>
<section class="section" style="padding-top: 4rem; padding-bottom: 4rem;">
    <div class="container" style="max-width: 1280px;">
        <div style="display: flex; gap: 1rem; margin-bottom: 2.5rem; justify-content: center; background: rgba(255, 255, 255, 0.5); padding: 0.5rem; border-radius: 50px; border: 1px solid rgba(0, 0, 0, 0.05); width: fit-content; margin-left: auto; margin-right: auto; flex-wrap: wrap;">
            <button onclick="switchTab('contacts')" id="tab-contacts" class="admin-nav-tab" style="padding: 0.8rem 2rem; border: none; cursor: pointer; border-radius: 40px; transition: all 0.3s; display: flex; align-items: center; gap: 0.8rem;">
                <i class="fas fa-envelope" style="font-size: 1.1rem;"></i> Contact List
            </button>
            <button onclick="switchTab('assessments')" id="tab-assessments" class="admin-nav-tab" style="padding: 0.8rem 2rem; border: none; cursor: pointer; border-radius: 40px; transition: all 0.3s; display: flex; align-items: center; gap: 0.8rem;">
                <i class="fas fa-clipboard-list" style="font-size: 1.1rem;"></i> Assessments
            </button>
            <button onclick="switchTab('blogs')" id="tab-blogs" class="admin-nav-tab" style="padding: 0.8rem 2rem; border: none; cursor: pointer; border-radius: 40px; transition: all 0.3s; display: flex; align-items: center; gap: 0.8rem;">
                <i class="fas fa-edit" style="font-size: 1.1rem;"></i> Blogs
            </button>
        </div>

        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 2rem;">Admin Data Manager</h2>

        <?php if (!empty($inlineErrorMessage)): ?>
            <div style="background: rgba(255, 0, 0, 0.1); color: #d32f2f; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; border: 1px solid #d32f2f; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-exclamation-circle"></i> <?= e((string) $inlineErrorMessage) ?>
            </div>
        <?php endif; ?>

        <div id="add-contact-panel" class="glass-card" style="margin-bottom: 2rem;">
            <button id="toggleAddContactForm" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(236, 72, 153, 0.2)); border: 1px solid var(--primary-color); border-radius: 10px; color: var(--primary-color); cursor: pointer; font-size: 1rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                <span><i class="fas fa-plus-circle"></i> Add New Contact</span>
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
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 0.5rem;">Total Contacts in DB</div>
                <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 800; line-height: 1;"><?= (int) ($totalContacts ?? 0) ?></div>
            </div>
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 0.5rem;">Total Assessments in DB</div>
                <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 800; line-height: 1;"><?= (int) ($totalAssessments ?? 0) ?></div>
            </div>
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; color: var(--accent-color); font-weight: 700; margin-bottom: 0.5rem;">Total Blogs in DB</div>
                <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 800; line-height: 1;"><?= (int) ($totalBlogs ?? 0) ?></div>
            </div>
        </div>

        <div id="contacts-content">
            <div class="glass-card" style="overflow-x: auto; padding: 0.5rem; background: white;">
                <?php if (empty($contacts)): ?>
                    <div style="text-align:center; padding:3rem; color:#64748b;">No contact submissions yet.</div>
                <?php else: ?>
                    <table style="width:100%; border-collapse: collapse; font-size:0.95rem; min-width:1000px;">
                        <thead>
                            <tr style="background: var(--primary-color); color:white;">
                                <th style="padding:1rem; text-align:left;">ID</th>
                                <th style="padding:1rem; text-align:left;">Name</th>
                                <th style="padding:1rem; text-align:left;">Email</th>
                                <th style="padding:1rem; text-align:left;">Phone</th>
                                <th style="padding:1rem; text-align:left;">Message</th>
                                <th style="padding:1rem; text-align:left;">Date</th>
                                <th style="padding:1rem; text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contactRow): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding:1rem; font-weight:700; color:var(--primary-color);">#<?= (int) $contactRow['id'] ?></td>
                                    <td style="padding:1rem;"><?= e((string) $contactRow['name']) ?></td>
                                    <td style="padding:1rem;"><?= e((string) $contactRow['email']) ?></td>
                                    <td style="padding:1rem;"><?= e((string) ($contactRow['phone'] ?: '-')) ?></td>
                                    <td style="padding:1rem;"><?= e(str_limit((string) $contactRow['message'], 90)) ?></td>
                                    <td style="padding:1rem;"><?= e(date('M d, Y H:i', strtotime((string) $contactRow['created_at']))) ?></td>
                                    <td style="padding:1rem; text-align:center; white-space:nowrap;">
                                        <button class="edit-btn" data-id="<?= (int) $contactRow['id'] ?>" data-name="<?= e((string) $contactRow['name']) ?>" data-email="<?= e((string) $contactRow['email']) ?>" data-phone="<?= e((string) $contactRow['phone']) ?>" data-message="<?= e((string) $contactRow['message']) ?>" style="padding:0.55rem 0.9rem; margin-right:0.3rem; background:#e0f2fe; border:1px solid #bae6fd; border-radius:6px; color:#0369a1; cursor:pointer;"><i class="fas fa-edit"></i> Edit</button>
                                        <button class="delete-btn" data-id="<?= (int) $contactRow['id'] ?>" style="padding:0.55rem 0.9rem; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; color:#b91c1c; cursor:pointer;"><i class="fas fa-trash"></i> Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div id="assessments-content" style="display:none;">
            <div class="glass-card" style="overflow-x: auto; padding: 0.5rem; background:white;">
                <?php if (empty($assessments)): ?>
                    <div style="text-align:center; padding:3rem; color:#64748b;">No assessments yet.</div>
                <?php else: ?>
                    <table style="width:100%; border-collapse: collapse; font-size:0.95rem; min-width:1000px;">
                        <thead>
                            <tr style="background: var(--accent-color); color:white;">
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

        <div id="blogs-content" style="display:none;">
            <div class="glass-card" style="overflow-x: auto; padding: 0.5rem; background: white;">
                <?php if (empty($blogs)): ?>
                    <div style="text-align:center; padding:3rem; color:#64748b;">No blog posts yet.</div>
                <?php else: ?>
                    <table style="width:100%; border-collapse: collapse; font-size:0.95rem; min-width:1000px;">
                        <thead>
                            <tr style="background: #3b82f6; color:white;">
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
</style>

<script>
    const booleanFields = <?= json_encode(array_values($booleanFields)) ?>;
    const activeTab = <?= json_encode($activeTab) ?>;
    const tabNames = ['contacts', 'assessments', 'blogs'];

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

        const addContactPanel = document.getElementById('add-contact-panel');
        const addBlogPanel = document.getElementById('add-blog-panel');

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

    function closeEditContactModal() {
        document.getElementById('editContactModal').style.display = 'none';
    }

    function closeDeleteContactModal() {
        document.getElementById('deleteContactModal').style.display = 'none';
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

    window.addEventListener('click', function (event) {
        ['editContactModal', 'deleteContactModal', 'editAssessmentModal', 'deleteAssessmentModal', 'editBlogModal', 'deleteBlogModal'].forEach(function (id) {
            const modal = document.getElementById(id);
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    switchTab(activeTab);
</script>
