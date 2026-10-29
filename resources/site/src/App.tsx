import React, { useCallback, useEffect, useState } from 'react';
import { SmoothScroll } from './components/SmoothScroll';
import { Header } from './components/Header';
import { HeroScrollPinned } from './components/HeroScrollPinned';
import { StatsBar } from './components/StatsBar';
import { WhyChooseConsultingSection } from './components/WhyChooseConsultingSection';
import { StrategicTargetSection } from './components/StrategicTargetSection';
import { PathwaySection } from './components/PathwaySection';
import { HeaderBannersSection } from './components/HeaderBannersSection';
import { ProcedureSection } from './components/ProcedureSection';
import { CEOMessageSection } from './components/CEOMessageSection';
import { LeadershipSection } from './components/LeadershipSection';
import { SuccessStoriesSection } from './components/SuccessStoriesSection';
import { StudyAbroadExposSection } from './components/StudyAbroadExposSection';
import { WaveMarquee } from './components/WaveMarquee';
import { CallToAction } from './components/CallToAction';
import { Footer } from './components/Footer';
import { InquiryModal } from './components/InquiryModal';
import { PortalModal } from './components/PortalModal';
import { FloatingActions } from './components/FloatingActions';
import { getContent } from './content';

export const App: React.FC = () => {
  const [modalOpen, setModalOpen] = useState(false);
  const [portalOpen, setPortalOpen] = useState(false);
  const [event, setEvent] = useState('');
  const [portalRole, setPortalRole] = useState<'student' | 'parent' | 'crm' | 'faculty'>('student');
  const openInquiry = useCallback((eventName?: string) => {
    setEvent(typeof eventName === 'string' ? eventName.slice(0, 180) : '');
    setModalOpen(true);
  }, []);
  const closeInquiry = useCallback(() => setModalOpen(false), []);
  const closePortal = useCallback(() => setPortalOpen(false), []);
  const openPortal = useCallback((role: 'student' | 'parent' | 'crm' | 'faculty' = 'student') => {
    setPortalRole(role);
    setPortalOpen(true);
  }, []);

  useEffect(() => {
    const openEvent = (e: Event) => {
      setEvent(String((e as CustomEvent).detail?.event || '').slice(0, 180));
      setModalOpen(true);
    };
    document.addEventListener('genz:inquiry', openEvent);
    return () => document.removeEventListener('genz:inquiry', openEvent);
  }, []);

  useEffect(() => {
    if (!modalOpen && !portalOpen) return;
    const previous = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    window.__lenis?.stop();
    return () => {
      document.body.style.overflow = previous;
      window.__lenis?.start();
    };
  }, [modalOpen, portalOpen]);

  return (
    <SmoothScroll>
      <div className="min-h-screen bg-cream font-sans text-navy selection:bg-cyan selection:text-white">
        <Header onOpenModal={openInquiry} onOpenPortalModal={openPortal} />
        <main>
          {/* Section 1: Hero 3D Interactive Plane & Globe + 60-Sec Quick Assessment Bar */}
          {getContent('sections.hero.enabled', true) && <HeroScrollPinned onOpenModal={openInquiry} />}

          {/* Section 2: Trust Metric Counter Bar & Accreditations */}
          {getContent('sections.stats.enabled', true) && <StatsBar />}

          {/* Section 3: Why Choose Us (Meezab Student Composite Photo + 4 Core Consulting Strengths) */}
          {getContent('sections.whyChoose.enabled', true) && <WhyChooseConsultingSection onOpenModal={openInquiry} />}

          {/* Section 4: Strategic Target (Paragon 3D Bullseye Dartboard Target + Precision Pathway) */}
          {getContent('sections.strategicTarget.enabled', true) && <StrategicTargetSection onOpenModal={openInquiry} />}

          {/* Section 5: Pathway Section (4 Steps: Counseling, Visa, Career, IELTS/PTE) */}
          {getContent('sections.pathway.enabled', true) && <PathwaySection onOpenModal={openInquiry} />}

          {/* Section 6: High-Resolution Header Banners Carousel */}
          {getContent('sections.headerBanners.enabled', true) && <HeaderBannersSection onOpenModal={openInquiry} />}

          {/* Section 7: Procedure Roadmap Section */}
          {getContent('sections.procedure.enabled', true) && <ProcedureSection onOpenModal={openInquiry} />}

          {/* Section 8: CEO Executive Message */}
          {getContent('sections.ceo.enabled', true) && <CEOMessageSection onOpenConsultation={openInquiry} />}

          {/* Section 9: Academic Leadership Board */}
          {getContent('sections.leadership.enabled', true) && <LeadershipSection onOpenConsultation={openInquiry} />}

          {/* Section 10: Verified Student Success Stories & Testimonials */}
          {getContent('sections.stories.enabled', true) && <SuccessStoriesSection />}

          {/* Section 11: Times Consultant-Style Study Abroad Expos 2026 */}
          {getContent('sections.expos.enabled', true) && <StudyAbroadExposSection onOpenModal={openInquiry} />}

          {/* Section 12: Partner Universities Marquee */}
          {getContent('sections.marquee.enabled', true) && <WaveMarquee />}

          {/* Section 13: 24h Callback Lead Form & Nationwide Branch Network */}
          {getContent('sections.callToAction.enabled', true) && <CallToAction onOpenModal={openInquiry} />}
        </main>
        <Footer onOpenModal={openInquiry} />
        <InquiryModal isOpen={modalOpen} onClose={closeInquiry} event={event} />
        <PortalModal isOpen={portalOpen} onClose={closePortal} initialRole={portalRole} />
        <FloatingActions onOpenModal={openInquiry} />
      </div>
    </SmoothScroll>
  );
};

export default App;
