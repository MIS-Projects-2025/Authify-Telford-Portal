import { useState, useEffect } from "react";
import axios from "axios";

/**
 * Global Memory Cache
 * This persists as long as the browser tab is open.
 * It prevents 429 errors by ensuring we only fetch each resource once.
 */
const portalCache = {
    departments: null,
    cards: {}, // Keyed by basename
    systems: {}, // Keyed by cardId
};

export function useDepartments() {
    const [departments, setDepartments] = useState(
        portalCache.departments || [],
    );
    const [loading, setLoading] = useState(!portalCache.departments);

    useEffect(() => {
        // If we already have departments in cache, don't make the API call
        if (portalCache.departments) {
            return;
        }

        axios
            .get("/api/departments")
            .then((res) => {
                portalCache.departments = res.data;
                setDepartments(res.data);
            })
            .catch((err) => console.error("Portal Error (Depts):", err))
            .finally(() => setLoading(false));
    }, []);

    return { departments, loading };
}

export function useCards(basename) {
    const [cards, setCards] = useState(portalCache.cards[basename] || []);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (!basename) return;

        // If this specific department is already cached, use it immediately
        if (portalCache.cards[basename]) {
            setCards(portalCache.cards[basename]);
            setLoading(false);
            return;
        }

        setLoading(true);
        axios
            .get(`/api/cards/${basename}`)
            .then((res) => {
                portalCache.cards[basename] = res.data;
                setCards(res.data);
            })
            .catch((err) => {
                if (err.response?.status === 429) {
                    // The Global Interceptor will show the Toast,
                    // but we log it here for debugging.
                    console.warn("Rate limit hit for cards.");
                }
            })
            .finally(() => setLoading(false));
    }, [basename]);

    return { cards, loading };
}

export function useSystems(cardId) {
    const [systems, setSystems] = useState(portalCache.systems[cardId] || []);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (!cardId) return;

        // Use cached systems if we've opened this card before
        if (portalCache.systems[cardId]) {
            setSystems(portalCache.systems[cardId]);
            setLoading(false);
            return;
        }

        setLoading(true);
        axios
            .get(`/api/systems/${cardId}`)
            .then((res) => {
                portalCache.systems[cardId] = res.data;
                setSystems(res.data);
            })
            .finally(() => setLoading(false));
    }, [cardId]);

    return { systems, loading };
}
