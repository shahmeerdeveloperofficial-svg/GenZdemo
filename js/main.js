/**
 * GenZ International School - Global Education & Career Development
 * Core Interactive Scripts & Scroll Animation Controller
 * Inspired by LA International & Central School International
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize all modules
  initNavbar();
  initContinuum();
  initAdmissionsBoard();
  initCounters();
  initModal();
  initFloatingActions();
  initMobileMenu();
});

/* ==========================================================================
   1. NAVBAR CONTROLLER
   ========================================================================== */
function initNavbar() {
  const navbar = document.querySelector('.navbar');
  const navLinks = document.querySelectorAll('.nav-link');
  const sections = document.querySelectorAll('section[id]');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }

    // Scroll spy
    let current = '';
    sections.forEach(section => {
      const sectionTop = section.offsetTop - 120;
      const sectionHeight = section.offsetHeight;
      if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${current}`) {
        link.classList.add('active');
      }
    });
  });
}

/* ==========================================================================
   2. CONTINUUM STAGE CONTROLLER (Signature LA International Feature)
   ========================================================================== */
const continuumData = [
  {
    stage: 'Stage 01',
    name: 'Early Years (Ages 2.5 - 5)',
    tag: 'Foundation & Play-Based Inception',
    desc: 'An inquiry-based exploratory environment where early learners discover wonder, bilingual vocabulary, emotional intelligence, and cognitive curiosity through sensory experiences and purposeful play.',
    pillars: [
      'Bilingual Language Immersion',
      'Montessori & Reggio Emilia Blend',
      'Sensory & Nature Exploration',
      'Foundational Phonics & Numeracy'
    ],
    badgeTitle: 'Cambridge Early Years Accredited',
    badgeDetail: 'Nurturing joyful, independent global thinkers from the earliest steps.',
    image: 'assets/images/early-years.jpg'
  },
  {
    stage: 'Stage 02',
    name: 'Primary Continuum (Grades 1 - 5)',
    tag: 'Project-Based Learning & Inquiry',
    desc: 'Inspired by world-leading inquiry models, our primary years ignite deep curiosity. Students connect mathematics, sciences, creative arts, and global cultures through real-world problem-solving.',
    pillars: [
      'Project-Based Learning (PBL)',
      'Singapore Mathematical Logic',
      'Junior Robotics & Coding',
      'Global Citizenship Curriculum'
    ],
    badgeTitle: 'CSI & IB Inquiry Standards',
    badgeDetail: 'Encouraging young minds to ask questions that reshape the future.',
    image: 'assets/images/primary-years.jpg'
  },
  {
    stage: 'Stage 03',
    name: 'Middle Years (Grades 6 - 8)',
    tag: 'Critical Inquiry & STEM Innovation',
    desc: 'Bridging foundational mastery with interdisciplinary rigor. Learners engage in hands-on scientific experimentation, Model United Nations, digital media arts, and community leadership.',
    pillars: [
      'Advanced STEM & 3D Prototyping',
      'Model United Nations (MUN)',
      'Interdisciplinary Sciences',
      'Debating & Rhetorical Arts'
    ],
    badgeTitle: 'Cambridge Lower Secondary',
    badgeDetail: 'Building resilience, intellectual agility, and ethical leadership.',
    image: 'assets/images/middle-years.jpg'
  },
  {
    stage: 'Stage 04',
    name: 'Senior Cambridge (IGCSE & A Levels)',
    tag: 'Academic Rigour & Global Credentials',
    desc: 'Recognized by the world’s elite universities, our senior programme offers specialized subject mastery, university-grade research colloquiums, and personalized portfolio development.',
    pillars: [
      'Cambridge IGCSE & A-Levels',
      'University Research Projects',
      'Individual Career Counseling',
      'Dual Diploma & Honors Tracks'
    ],
    badgeTitle: 'Cambridge International Fellow',
    badgeDetail: 'Consistently producing World & Country distinction holders.',
    image: 'assets/images/senior-cambridge.jpg'
  },
  {
    stage: 'Stage 05',
    name: 'Beyond GenZ: University Flight Path',
    tag: 'Global Ivy League & Russell Group Launch',
    desc: 'Embodying the soaring airplane of our logo, our dedicated Global Advisory wing guides scholars to top universities in the US, UK, Canada, Australia, and Europe with comprehensive scholarship strategy.',
    pillars: [
      'Ivy League & Russell Group Prep',
      'SAT / ACT & IELTS Coaching',
      'Full-Tuition Scholarship Strategy',
      'Worldwide Alumni Network'
    ],
    badgeTitle: 'Global Placement Guarantee',
    badgeDetail: 'Over $12M in global university scholarships secured for graduates.',
    image: 'assets/images/beyond-pathway.jpg'
  }
];

function initContinuum() {
  const tabButtons = document.querySelectorAll('.continuum-tab-btn');
  const stageTag = document.getElementById('stageTag');
  const stageTitle = document.getElementById('stageTitle');
  const stageDesc = document.getElementById('stageDesc');
  const stagePillars = document.getElementById('stagePillars');
  const stageImage = document.getElementById('stageImage');
  const stageBadgeTitle = document.getElementById('stageBadgeTitle');
  const stageBadgeDetail = document.getElementById('stageBadgeDetail');

  if (!tabButtons.length || !stageTitle) return;

  function renderStage(index) {
    const data = continuumData[index];
    if (!data) return;

    tabButtons.forEach((btn, idx) => {
      btn.classList.toggle('is-active', idx === index);
    });

    // Smooth transition
    const cardContent = document.querySelector('.continuum-card-content');
    const cardVisual = document.querySelector('.continuum-card-visual');
    if (cardContent) cardContent.style.opacity = '0.4';
    if (cardVisual) cardVisual.style.opacity = '0.4';

    setTimeout(() => {
      stageTag.textContent = `${data.stage} · ${data.tag}`;
      stageTitle.textContent = data.name;
      stageDesc.textContent = data.desc;
      stageBadgeTitle.textContent = data.badgeTitle;
      stageBadgeDetail.textContent = data.badgeDetail;
      stageImage.src = data.image;
      stageImage.alt = data.name;

      // Update pillars
      stagePillars.innerHTML = data.pillars.map(p => `
        <li class="continuum-pillar-item">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>${p}</span>
        </li>
      `).join('');

      if (cardContent) cardContent.style.opacity = '1';
      if (cardVisual) cardVisual.style.opacity = '1';
    }, 180);
  }

  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const stageIdx = parseInt(btn.getAttribute('data-stage'), 10);
      renderStage(stageIdx);
    });
  });
}

/* ==========================================================================
   3. ADMISSIONS FLIGHT PLAN BOARD (LA International Mac Board Style)
   ========================================================================== */
const admissionsData = [
  {
    step: '01',
    title: 'Initial Discovery & Counseling',
    heading: 'Step 1: Discover Your Child\'s Potential',
    text: 'Schedule an introductory consultation with our admissions deans. We examine academic history, individual passions, and outline tailored learning pathways across our Cambridge & Global continuum.',
    buttonText: 'Schedule Discovery Call',
    badge: 'Estimated Time: 30 mins'
  },
  {
    step: '02',
    title: 'Personalized Campus Experience & Assessment',
    heading: 'Step 2: Guided Tour & Diagnostic Inquiry',
    text: 'Experience our innovation labs, sports complex, and interactive classrooms. Prospective scholars participate in a supportive, age-appropriate assessment celebrating logic, language, and creativity.',
    buttonText: 'Book Campus Tour',
    badge: 'Weekday & Saturday Slots'
  },
  {
    step: '03',
    title: 'Leadership & Family Dialogue',
    heading: 'Step 3: Vision Alignment Interview',
    text: 'A collaborative discussion between parents, student, and school leadership to ensure educational aspirations, ethical values, and mutual commitments harmonize for long-term growth.',
    buttonText: 'Confirm Interview Slot',
    badge: 'Virtual or On-Campus'
  },
  {
    step: '04',
    title: 'Enrolment & Global Flight Pack',
    heading: 'Step 4: Welcome to the GenZ Family!',
    text: 'Upon acceptance, families receive our comprehensive Scholar Onboarding Flight Pack, complete with portal access credentials, uniform fittings, house assignment, and academic calendar.',
    buttonText: 'Access Student Portal',
    badge: 'Immediate Confirmation'
  }
];

function initAdmissionsBoard() {
  const stepCards = document.querySelectorAll('.adm-step-card');
  const detailsHeading = document.getElementById('admDetailsHeading');
  const detailsText = document.getElementById('admDetailsText');
  const detailsBtn = document.getElementById('admDetailsBtn');
  const detailsBadge = document.getElementById('admDetailsBadge');

  if (!stepCards.length || !detailsHeading) return;

  stepCards.forEach((card, idx) => {
    card.addEventListener('click', () => {
      stepCards.forEach(c => c.classList.remove('is-selected'));
      card.classList.add('is-selected');

      const data = admissionsData[idx];
      if (data) {
        detailsHeading.textContent = data.heading;
        detailsText.textContent = data.text;
        if (detailsBtn) detailsBtn.innerHTML = `<span>${data.buttonText}</span> <span class="btn-dot"></span>`;
        if (detailsBadge) detailsBadge.textContent = data.badge;
      }
    });
  });
}

/* ==========================================================================
   4. ANIMATED STAT COUNTERS
   ========================================================================== */
function initCounters() {
  const counterElements = document.querySelectorAll('.stat-number[data-target]');
  if (!counterElements.length) return;

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseFloat(el.getAttribute('data-target'));
        const isRatio = el.getAttribute('data-ratio');
        const duration = 1800;
        const start = 0;
        const startTime = performance.now();

        function updateCounter(currentTime) {
          const elapsed = currentTime - startTime;
          const progress = Math.min(elapsed / duration, 1);
          const easeOut = 1 - Math.pow(1 - progress, 3);
          const currentVal = Math.floor(start + (target - start) * easeOut);

          if (isRatio) {
            el.textContent = `1:${Math.max(1, Math.round(target * easeOut))}`;
          } else {
            el.textContent = currentVal.toLocaleString();
          }

          if (progress < 1) {
            requestAnimationFrame(updateCounter);
          } else {
            if (isRatio) {
              el.textContent = `1:${target}`;
            } else {
              el.textContent = target.toLocaleString();
            }
          }
        }

        requestAnimationFrame(updateCounter);
        obs.unobserve(el);
      }
    });
  }, { threshold: 0.4 });

  counterElements.forEach(el => observer.observe(el));
}

/* ==========================================================================
   5. ADMISSION INQUIRY & CAMPUS TOUR MODAL
   ========================================================================== */
function initModal() {
  const modalOverlay = document.getElementById('inquiryModal');
  const openButtons = document.querySelectorAll('[data-open-modal]');
  const closeBtn = document.getElementById('modalCloseBtn');
  const inquiryForm = document.getElementById('inquiryForm');
  const successScreen = document.getElementById('modalSuccessScreen');

  if (!modalOverlay) return;

  function openModal() {
    modalOverlay.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modalOverlay.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  openButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      openModal();
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', closeModal);
  }

  modalOverlay.addEventListener('click', (e) => {
    if (e.target === modalOverlay) closeModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modalOverlay.classList.contains('is-open')) {
      closeModal();
    }
  });

  if (inquiryForm) {
    inquiryForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const submitBtn = inquiryForm.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.innerHTML = 'Submitting Flight Plan...';
        submitBtn.disabled = true;
      }

      setTimeout(() => {
        inquiryForm.style.display = 'none';
        if (successScreen) successScreen.style.display = 'block';
      }, 900);
    });
  }
}

/* ==========================================================================
   6. FLOATING ACTION CONTROLS & BACK TO TOP
   ========================================================================== */
function initFloatingActions() {
  const backToTopBtn = document.getElementById('backToTopBtn');
  if (!backToTopBtn) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 400) {
      backToTopBtn.classList.add('is-visible');
    } else {
      backToTopBtn.classList.remove('is-visible');
    }
  });

  backToTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

/* ==========================================================================
   7. MOBILE NAVIGATION DRAWER
   ========================================================================== */
function initMobileMenu() {
  const menuToggle = document.getElementById('mobileMenuToggle');
  const navLinks = document.querySelector('.navbar-links');

  if (!menuToggle || !navLinks) return;

  menuToggle.addEventListener('click', () => {
    const isExpanded = navLinks.classList.toggle('mobile-open');
    menuToggle.setAttribute('aria-expanded', isExpanded);
    if (isExpanded) {
      navLinks.style.display = 'flex';
      navLinks.style.flexDirection = 'column';
      navLinks.style.position = 'absolute';
      navLinks.style.top = '100%';
      navLinks.style.left = '0';
      navLinks.style.width = '100%';
      navLinks.style.background = '#ffffff';
      navLinks.style.padding = '24px';
      navLinks.style.boxShadow = '0 20px 40px rgba(0,0,0,0.1)';
      navLinks.style.borderTop = '1px solid #e2e8f0';
    } else {
      navLinks.style.display = '';
    }
  });
}
