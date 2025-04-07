function user() {
    return "jaydev";
}

function displayMediaType(fileUrl, fileType, fileName, senderName) {
    if (!fileType || !fileUrl) {
        return `<p class="text-sm text-gray-500">Unsupported file type</p>`;
    }

    fileType = fileType.toLowerCase();
    let displayName = fileName.includes(" || ")
        ? fileName.split(" || ")[1].trim()
        : fileName;
    let mediaHtml = "";

    if (fileType.startsWith("image")) {
        mediaHtml = `
<img src="${fileUrl}" class="w-40 h-40 rounded-lg border" alt="Sent Image">
<a href="${fileUrl}" download="${displayName}" class="block text-blue-500 mt-1 text-sm">Download Image</a>
`;
    } else if (fileType.startsWith("video")) {
        mediaHtml = `
<video controls class="w-40 h-40 rounded-lg border">
    <source src="${fileUrl}" type="${fileType}">
    Your browser does not support the video tag.
</video>
<a href="${fileUrl}" download="${displayName}" class="block text-blue-500 mt-1 text-sm">Download Video</a>
`;
    } else if (fileType.startsWith("audio")) {
        mediaHtml = `
<audio controls class="w-40">
    <source src="${fileUrl}" type="${fileType}">
    Your browser does not support the audio tag.
</audio>
<a href="${fileUrl}" download="${displayName}" class="block text-blue-500 mt-1 text-sm">Download Audio</a>
`;
    } else if (fileType === "application/pdf") {
        mediaHtml = `
<div class="flex items-center space-x-2 bg-gray-300 dark:bg-gray-700 p-2 rounded-lg max-w-xs">
    <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" class="w-10 h-10" alt="PDF Icon">
    <span class="truncate max-w-[180px]" title="${displayName}">
        <a href="${fileUrl}" target="_blank" class="text-white underline">${displayName}</a>
    </span>
</div>
<a href="${fileUrl}" download="${displayName}" class="block text-blue-500 mt-1 text-sm">Download PDF</a>
`;
    } else {
        mediaHtml = `
<a href="${fileUrl}" target="_blank" class="text-blue-500 underline truncate max-w-[200px]" title="${displayName}">${displayName}</a>
<a href="${fileUrl}" download="${displayName}" class="block text-blue-500 mt-1 text-sm">Download File</a>
`;
    }

    return `
<div class="rounded-lg p-2 max-w-xs bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
${mediaHtml}
</div>
`;
}
