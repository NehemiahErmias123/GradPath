import { useState, useEffect } from "react";
import { API_BASE_URL } from "../config";

function Programs() {
  const [search, setSearch] = useState("");
  const [results, setResults] = useState([]);
  const [searching, setSearching] = useState(false);
  const [message, setMessage] = useState(null);

  useEffect(() => {
    if (!search.trim()) {
      setResults([]);
      return;
    }

    setSearching(true);
    const timeoutId = setTimeout(() => {
      fetch(
        `${API_BASE_URL}/programs/search-universities.php?name=${encodeURIComponent(search)}`,
      )
        .then((res) => res.json())
        .then((data) => {
          setResults(Array.isArray(data) ? data : []);
          setSearching(false);
        })
        .catch(() => setSearching(false));
    }, 400);

    return () => clearTimeout(timeoutId);
  }, [search]);

  useEffect(() => {
    if (!message) return;
    const timeoutId = setTimeout(() => setMessage(null), 3000);
    return () => clearTimeout(timeoutId);
  }, [message]);

  const trackProgram = async (university) => {
    const token = localStorage.getItem("token");

    const findResponse = await fetch(
      `${API_BASE_URL}/programs/find-or-create.php`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          university_name: university.name,
          country: university.country,
        }),
      },
    );
    const findData = await findResponse.json();

    if (!findResponse.ok) {
      setMessage({ type: "error", text: findData.error });
      return;
    }

    const trackResponse = await fetch(
      `${API_BASE_URL}/tracked-programs/track.php`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ program_id: findData.program_id }),
      },
    );
    const trackData = await trackResponse.json();

    if (!trackResponse.ok) {
      setMessage({ type: "error", text: trackData.error });
      return;
    }

    setMessage({
      type: "success",
      text: `Added ${university.name} to your tracker.`,
    });
  };

  return (
    <div className="p-8 max-w-4xl mx-auto">
      <h1 className="font-serif text-4xl text-ink mb-2">Browse programs</h1>
      <p className="text-muted text-base mb-8">
        Search real universities worldwide and add them to your tracker.
      </p>

      {message && (
        <div
          className={`mb-6 px-5 py-3 rounded-lg text-base ${
            message.type === "success"
              ? "bg-green-light text-green"
              : "bg-red-50 text-red-700 border border-red-200"
          }`}
        >
          {message.text}
        </div>
      )}

      <input
        type="text"
        placeholder="Search any university worldwide..."
        value={search}
        onChange={(e) => setSearch(e.target.value)}
        className="w-full bg-paper/90 border border-hairline px-5 py-4 rounded-xl mb-8 text-base shadow-sm focus:outline-none focus:border-green focus:bg-white focus:shadow-md transition-all duration-200"
      />

      {!search.trim() && (
        <p className="text-muted text-lg">
          Start typing to search universities worldwide.
        </p>
      )}

      {searching && <p className="text-muted text-lg">Searching...</p>}

      <div className="grid gap-5">
        {results.map((uni, index) => (
          <div
            key={index}
            className="bg-white rounded-xl p-6 flex justify-between items-center border border-hairline shadow-sm hover:shadow-lg hover:border-green hover:-translate-y-0.5 transition-all duration-200"
          >
            <div>
              <h2 className="font-serif text-2xl text-ink">{uni.name}</h2>
              <p className="text-base text-muted mt-1">{uni.country}</p>
            </div>
            <button
              onClick={() => trackProgram(uni)}
              className="bg-brass text-white px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-200 hover:scale-105 hover:opacity-90 shrink-0"
            >
              Track
            </button>
          </div>
        ))}
      </div>

      {search.trim() && !searching && results.length === 0 && (
        <p className="text-muted text-lg">No universities found.</p>
      )}
    </div>
  );
}

export default Programs;
