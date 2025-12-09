document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-cg-recommender-root]');
    if (!root) return;

    const state = {
        intent: 'any',
        vibe: 'any',
        constraints: [],
        group: 'any',
        lat: null,
        lng: null,
    };

    const totalSteps = 4;
    let currentStep = 1;

    const stepIndicator = root.querySelector('[data-cg-step-indicator]');
    const steps = Array.from(root.querySelectorAll('.cg-quiz-step'));
    const submitBtn = root.querySelector('[data-cg-quiz-submit]');
    const nextBtn = root.querySelector('[data-cg-quiz-next]');
    const prevBtn = root.querySelector('[data-cg-quiz-prev]');

    function updateStepView() {
        steps.forEach(step => {
            const stepNum = parseInt(step.dataset.cgStep || '0', 10);
            if (stepNum === currentStep) {
                step.classList.remove('hidden');
            } else {
                step.classList.add('hidden');
            }
        });
        if (stepIndicator) {
            stepIndicator.textContent = `Step ${currentStep} of ${totalSteps}`;
        }
        if (prevBtn) prevBtn.disabled = currentStep === 1;
        if (nextBtn) nextBtn.disabled = currentStep === totalSteps;
    }

    function setActiveChip(stepNumber, target) {
        const chips = root.querySelectorAll(`.cg-quiz-chip[data-cg-step="${stepNumber}"]`);
        chips.forEach(chip => chip.classList.remove('active'));
        target.classList.add('active');
    }

    function toggleMultiChip(target) {
        target.classList.toggle('active');
        const value = target.dataset.cgAnswer;
        if (!value) return;
        if (state.constraints.includes(value)) {
            state.constraints = state.constraints.filter(c => c !== value);
        } else {
            state.constraints.push(value);
        }
    }

    function handleChipClick(event) {
        const chip = event.currentTarget;
        const stepNumber = parseInt(chip.dataset.cgStep || '0', 10);
        const value = chip.dataset.cgAnswer;
        if (!stepNumber || !value) return;

        if (stepNumber === 3) {
            toggleMultiChip(chip);
        } else {
            setActiveChip(stepNumber, chip);
            if (stepNumber === 1) state.intent = value;
            if (stepNumber === 2) state.vibe = value;
            if (stepNumber === 4) state.group = value;
            if (stepNumber < totalSteps) {
                currentStep = Math.min(totalSteps, currentStep + 1);
                updateStepView();
            }
        }

        if (stepNumber === 1) state.intent = value;
        if (stepNumber === 2) state.vibe = value;
        if (stepNumber === 4) state.group = value;
    }

    function buildQueryString() {
        const params = new URLSearchParams();
        params.set('cg_intent', state.intent || 'any');
        params.set('cg_vibe', state.vibe || 'any');
        params.set('cg_group', state.group || 'any');

        if (state.constraints.length > 0) {
            params.set('cg_constraints', state.constraints.join(','));
        }

        if (state.lat !== null && state.lng !== null) {
            params.set('cg_lat', state.lat);
            params.set('cg_lng', state.lng);
        }

        return params.toString();
    }

    function redirectWithParams() {
        const baseUrl = window.cgRecommenderData?.recommendationsUrl || '/recommendations/';
        const qs = buildQueryString();
        window.location.href = qs ? `${baseUrl}?${qs}` : baseUrl;
    }

    function handleSubmit() {
        const wantsNear = state.constraints.includes('near');
        if (!wantsNear || !navigator.geolocation) {
            redirectWithParams();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = submitBtn.dataset.cgSubmittingLabel || 'Finding your location...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                state.lat = pos.coords.latitude;
                state.lng = pos.coords.longitude;
                redirectWithParams();
            },
            () => {
                redirectWithParams();
            }
        );
    }

    root.querySelectorAll('.cg-quiz-chip').forEach(chip => {
        chip.addEventListener('click', handleChipClick);
    });

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentStep = Math.min(totalSteps, currentStep + 1);
            updateStepView();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentStep = Math.max(1, currentStep - 1);
            updateStepView();
        });
    }

    if (submitBtn) {
        submitBtn.dataset.cgSubmittingLabel = submitBtn.textContent;
        submitBtn.addEventListener('click', handleSubmit);
    }

    updateStepView();
});
