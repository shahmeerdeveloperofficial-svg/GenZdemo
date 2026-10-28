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
import { LMSAndCRMSection } from './components/LMSAndCRMSection';
import { PhilosophySection } from './components/PhilosophySection';
import { WhyUsSection } from './components/WhyUsSection';
import { CampusLifeSection } from './components/CampusLifeSection';
import { SuccessStoriesSection } from './components/SuccessStoriesSection';
import { AdmissionsFlightPlan } from './components/AdmissionsFlightPlan';
import { WelcomeSection } from './components/WelcomeSection';
import { CatalogSection } from './components/CatalogSection';
import { WaveMarquee } from './components/WaveMarquee';
import { NewsEventsSection } from './components/NewsEventsSection';
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
  const openInquiry = useCallback(() => { setEvent(''); setModalOpen(true); }, []);
  const closeInquiry = useCallback(() => setModalOpen(false), []);
  const closePortal = useCallback(() => setPortalOpen(false), []);
  const openPortal = useCallback((role: 'student' | 'parent' | 'crm' | 'faculty' = 'student') => { setPortalRole(role); setPortalOpen(true); }, []);

  useEffect(() => {
    const openEvent = (e: Event) => { setEvent(String((e as CustomEvent).detail?.event || '').slice(0, 180)); setModalOpen(true); };
    document.addEventListener('genz:inquiry', openEvent);
    return () => document.removeEventListener('genz:inquiry', openEvent);
  }, []);

  useEffect(() => {
    if (!modalOpen && !portalOpen) return;
    const previous = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    window.__lenis?.stop();
    return () => { document.body.style.overflow = previous; window.__lenis?.start(); };
  }, [modalOpen, portalOpen]);

  return (
    <SmoothScroll>
      <div className="min-h-screen bg-cream font-sans text-navy selection:bg-cyan selection:text-white">
        <Header onOpenModal={openInquiry} onOpenPortalModal={openPortal} />
        <main>
          {getContent('sections.hero.enabled', true) && <HeroScrollPinned onOpenModal={openInquiry} />}
          {getContent('sections.stats.enabled', true) && <StatsBar />}

          {/* New sections between StatsBar and CEOMessageSection */}
          {getContent('sections.whyChoose.enabled', true) && <WhyChooseConsultingSection onOpenModal={openInquiry} />}
          {getContent('sections.strategicTarget.enabled', true) && <StrategicTargetSection onOpenModal={openInquiry} />}
          {getContent('sections.pathway.enabled', true) && <PathwaySection onOpenModal={openInquiry} />}
          {getContent('sections.headerBanners.enabled', true) && <HeaderBannersSection onOpenModal={openInquiry} />}
          {getContent('sections.procedure.enabled', true) && <ProcedureSection onOpenModal={openInquiry} />}

          {getContent('sections.ceo.enabled', true) && <CEOMessageSection onOpenConsultation={openInquiry} />}
          {getContent('sections.leadership.enabled', true) && <LeadershipSection onOpenConsultation={openInquiry} />}
          {getContent('sections.platform.enabled', true) && <LMSAndCRMSection onOpenPortalModal={openPortal} />}
          {getContent('sections.philosophy.enabled', true) && <PhilosophySection />}
          {getContent('sections.benefits.enabled', true) && <WhyUsSection />}
          {getContent('sections.campus.enabled', true) && <CampusLifeSection onOpenModal={openInquiry} />}
          {getContent('sections.stories.enabled', true) && <SuccessStoriesSection />}
          {getContent('sections.admissions.enabled', true) && <AdmissionsFlightPlan onOpenModal={openInquiry} />}
          {getContent('sections.welcome.enabled', true) && <WelcomeSection />}
          {getContent('sections.catalog.enabled', true) && <CatalogSection />}
          {getContent('sections.marquee.enabled', true) && <WaveMarquee />}
          {getContent('sections.news.enabled', true) && <NewsEventsSection onOpenModal={openInquiry} />}
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
