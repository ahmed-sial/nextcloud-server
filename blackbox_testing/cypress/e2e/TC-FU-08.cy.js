describe("TC-FU-08", () => {
    it("TC-FU-08", () => {
        cy.viewport(1089, 825);
        cy.visit("http://localhost:8080/apps/files/files");

        cy.get("div.files-list__header > div > div svg").click();
        cy.get("li.active span.action-button__longtext-wrapper > span").click();

        // Create a filename with 256 characters
        const longFilename = 'a'.repeat(256) + '.txt';

        cy.get('input[data-cy-upload-picker-input][type="file"]')
            .selectFile({
                fileName: longFilename,
                contents: 'Test content for long filename'
            }, { force: true });

        // Verify error message for filename too long
        cy.contains("Error: Filename too long").should("be.visible");
    });
});